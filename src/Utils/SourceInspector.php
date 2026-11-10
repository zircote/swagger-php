<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Utils;

use OpenApi\Contracts\SourceInspectorInterface;

/**
 * Answers {@see SourceInspectorInterface} from reflection, and finds a file's classes from its
 * tokens, so nothing is parsed.
 *
 * A method taken from a trait reports the trait's file and lines, so a method is the class's own
 * when its lines lie inside the class. Properties and constants have no lines. PHP lets a class
 * redeclare one a trait declares only compatibly, so it is the class's own when no direct trait
 * declares it with the same doc comment and attributes.
 *
 * Reflection cannot tell a property or constant a class redeclares word for word from the copy a
 * trait gives it, nor a method of a trait sharing a line with the class. Where a direct trait
 * holds the same declaration, or a trait shares the class's lines, the class body's tokens decide.
 */
class SourceInspector implements SourceInspectorInterface
{
    private const DECLARATIONS = [T_CLASS, T_INTERFACE, T_TRAIT, T_ENUM];

    /**
     * The members each class's body declares, by class name, for the cases reflection cannot settle.
     *
     * @var array<string, array{methods: list<string>, properties: list<string>, consts: list<string>}>
     */
    private array $written = [];

    /**
     * The last file read for its class bodies, and its tokens.
     *
     * @var array{string, list<\PhpToken>}|null
     */
    private ?array $lastRead = null;

    public function classesIn(string $filename): array
    {
        $tokens = $this->tokens($filename);

        $classes = [];
        $namespace = '';
        // the depth of a declaration that counts: the file's top level, or a braced namespace's
        $top = 0;
        $depth = 0;
        // inside parentheses or brackets, attributes included, `namespace` or `class` is an
        // argument name
        $nested = 0;
        foreach ($tokens as $index => $token) {
            if ($token->is([T_CURLY_OPEN, T_DOLLAR_OPEN_CURLY_BRACES]) || $token->text === '{') {
                ++$depth;
            } elseif ($token->text === '}') {
                --$depth;
            } elseif ($token->is(T_ATTRIBUTE) || $token->text === '(' || $token->text === '[') {
                ++$nested;
            } elseif ($token->text === ')' || $token->text === ']') {
                --$nested;
            } elseif ($nested > 0) {
                continue;
            } elseif ($token->is(T_NAMESPACE) && $depth === 0) {
                $name = $tokens[$index + 1] ?? null;
                $namespace = $name?->is([T_STRING, T_NAME_QUALIFIED]) ? $name->text : '';
                $top = $this->opensBlock($tokens, $index) ? 1 : 0;
            } elseif ($token->is(self::DECLARATIONS) && $depth === $top) {
                $name = $tokens[$index + 1] ?? null;
                $previous = $tokens[$index - 1] ?? null;
                if ($name?->is(T_STRING) && !$previous?->is([T_DOUBLE_COLON, T_NEW])) {
                    /** @var class-string $class */
                    $class = ltrim($namespace . '\\' . $name->text, '\\');
                    $classes[] = $class;
                }
            }
        }

        return $classes;
    }

    public function declares(\ReflectionClass $class, \ReflectionMethod|\ReflectionProperty|\ReflectionClassConstant $member): bool
    {
        if ($member->getDeclaringClass()->getName() !== $class->getName()) {
            return false;
        }

        if ($member instanceof \ReflectionMethod) {
            if (!$this->withinLines($member, $class)) {
                return false;
            }

            foreach ($this->allTraits($class) as $trait) {
                if ($this->withinLines($member, $trait)) {
                    return in_array($member->getName(), $this->written($class)['methods'], true);
                }
            }

            return true;
        }

        if ($member instanceof \ReflectionProperty ? $class->isEnum() : $member->isEnumCase()) {
            // an enum's `name` and `value` come with it, and a case is not a constant it declares
            return false;
        }

        foreach ($class->getTraits() as $trait) {
            $declared = $member instanceof \ReflectionProperty
                ? ($trait->hasProperty($member->getName()) ? $trait->getProperty($member->getName()) : null)
                : ($trait->getReflectionConstant($member->getName()) ?: null);

            if ($declared !== null && $this->sameDeclaration($member, $declared)) {
                $written = $this->written($class);

                return in_array($member->getName(), $member instanceof \ReflectionProperty ? $written['properties'] : $written['consts'], true);
            }
        }

        return true;
    }

    public function directTraits(\ReflectionClass $class): array
    {
        return array_values($class->getTraits());
    }

    /**
     * The tokens of a file, without whitespace, comments and text outside code.
     *
     * A brace in a string or around the PHP tags is text, not structure.
     *
     * @return list<\PhpToken>
     */
    protected function tokens(string $filename): array
    {
        $code = is_file($filename) && is_readable($filename) ? file_get_contents($filename) : false;
        if (false === $code) {
            throw new \RuntimeException("Unable to read {$filename}");
        }

        return array_values(array_filter(
            \PhpToken::tokenize($code),
            static fn (\PhpToken $token): bool => !$token->is([T_WHITESPACE, T_COMMENT, T_DOC_COMMENT, T_ENCAPSED_AND_WHITESPACE, T_INLINE_HTML]),
        ));
    }

    /**
     * @param \ReflectionClass<object> $class
     */
    private function withinLines(\ReflectionMethod $method, \ReflectionClass $class): bool
    {
        return $method->getFileName() === $class->getFileName()
            && $method->getStartLine() >= $class->getStartLine()
            && $method->getEndLine() <= $class->getEndLine();
    }

    /**
     * The traits of a class, and theirs in turn.
     *
     * @param \ReflectionClass<object> $class
     *
     * @return list<\ReflectionClass<object>>
     */
    private function allTraits(\ReflectionClass $class): array
    {
        $traits = [];
        foreach ($class->getTraits() as $trait) {
            array_push($traits, $trait, ...$this->allTraits($trait));
        }

        return $traits;
    }

    /**
     * The methods, properties and constants written in the class's body.
     *
     * @param \ReflectionClass<object> $class
     *
     * @return array{methods: list<string>, properties: list<string>, consts: list<string>}
     */
    private function written(\ReflectionClass $class): array
    {
        $name = $class->getName();
        if (isset($this->written[$name])) {
            return $this->written[$name];
        }

        $written = ['methods' => [], 'properties' => [], 'consts' => []];
        // code from `eval()` names a file that does not exist, and the trait's copy wins
        $filename = $class->getFileName();
        if ($filename === false || !is_file($filename)) {
            $tokens = [];
        } else {
            // the classes of one file are usually asked about one after another
            if ($this->lastRead === null || $this->lastRead[0] !== $filename) {
                $this->lastRead = [$filename, $this->tokens($filename)];
            }
            $tokens = $this->lastRead[1];
        }
        $start = $this->bodyStart($tokens, $class);

        $depth = 1;
        $nested = 0;
        for ($index = $start ?? count($tokens); isset($tokens[$index]) && $depth > 0; ++$index) {
            $token = $tokens[$index];
            if ($token->is([T_CURLY_OPEN, T_DOLLAR_OPEN_CURLY_BRACES]) || $token->text === '{') {
                ++$depth;
            } elseif ($token->text === '}') {
                --$depth;
            } elseif ($token->is(T_ATTRIBUTE) || $token->text === '(' || $token->text === '[') {
                ++$nested;
            } elseif ($token->text === ')' || $token->text === ']') {
                --$nested;
            } elseif ($depth > 1 || $nested > 0) {
                continue;
            } elseif ($token->is(T_FUNCTION)) {
                $next = $tokens[$index + 1] ?? null;
                $next = $next?->text === '&' ? $tokens[$index + 2] ?? null : $next;
                if ($next instanceof \PhpToken) {
                    $written['methods'][] = $next->text;
                }
            } elseif ($token->is(T_VARIABLE)) {
                $written['properties'][] = substr($token->text, 1);
            } elseif ($token->is(T_CONST)) {
                // each name stands before its `=`, after an optional type
                for ($at = $index + 1; isset($tokens[$at]) && $tokens[$at]->text !== ';'; ++$at) {
                    if ($tokens[$at]->text === '=') {
                        $written['consts'][] = $tokens[$at - 1]->text;
                    }
                }
            }
        }

        return $this->written[$name] = $written;
    }

    /**
     * The index of the first token inside the class's braces, or null if the file does not hold it.
     *
     * @param list<\PhpToken>          $tokens
     * @param \ReflectionClass<object> $class
     */
    private function bodyStart(array $tokens, \ReflectionClass $class): ?int
    {
        foreach ($tokens as $index => $token) {
            if ($token->is(self::DECLARATIONS)
                && $token->line >= $class->getStartLine()
                && ($tokens[$index + 1] ?? null)?->text === $class->getShortName()) {
                for ($next = $index + 2; isset($tokens[$next]); ++$next) {
                    if ($tokens[$next]->text === '{') {
                        return $next + 1;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Whether the namespace statement at `$index` opens a block, `namespace Foo { ... }`.
     *
     * @param list<\PhpToken> $tokens
     */
    private function opensBlock(array $tokens, int $index): bool
    {
        for ($next = $index + 1; isset($tokens[$next]); ++$next) {
            if ($tokens[$next]->text === '{') {
                return true;
            }

            if ($tokens[$next]->text === ';' || $tokens[$next]->is(T_CLOSE_TAG)) {
                return false;
            }
        }

        return false;
    }

    /**
     * Whether two declarations carry the same doc comment and attributes, by name.
     *
     * Arguments are left unread: reading them evaluates them, and a class they name may not exist.
     */
    private function sameDeclaration(
        \ReflectionProperty|\ReflectionClassConstant $member,
        \ReflectionProperty|\ReflectionClassConstant $declared,
    ): bool {
        $attributes = static fn (\ReflectionProperty|\ReflectionClassConstant $reflector): array => array_map(
            static fn (\ReflectionAttribute $attribute): string => $attribute->getName(),
            $reflector->getAttributes(),
        );

        return $member->getDocComment() === $declared->getDocComment()
            && $attributes($member) === $attributes($declared);
    }
}
