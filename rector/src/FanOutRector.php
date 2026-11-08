<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Rector;

use OpenApi\Contracts\AttributeInterface;
use OpenApi\Spec\Fan;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Attribute;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * Adds `fan: Fan::All` to an attribute that classic put into every attribute able to hold it.
 *
 * Classic nests an attribute into every sibling that can take it: a `Response` stacked beside a
 * `Get` and a `Post` goes into both operations, and a `Header` beside two responses into both.
 * A parameter's attribute goes into every operation on its method. Spec takes one match, and
 * skips the method with an ambiguous merge where there are several. `fan: Fan::All` is spec's
 * spelling for classic's behaviour, so the migrated document keeps what classic wrote.
 *
 * Matches are worked out the way spec resolves them: siblings by `merge()`, held back while
 * another sibling still merges into them, and a parameter's attributes against the method's by
 * `contained()`. An attribute with one match, with `fan:` already given, or whose matches share
 * no type is left alone. Spec reports the last case whatever `fan:` says.
 *
 * Both names are matched: on the pass that renames the class this rule sees the classic one.
 */
final class FanOutRector extends AbstractRector
{
    /** @var array<string, string>|null classic class => spec class */
    private static ?array $classes = null;

    /** @var array<string, array{merge: array<string, string>, contained: array<string, string>}> */
    private static array $targets = [];

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Add fan: Fan::All where classic put an attribute into every attribute able to hold it', [new CodeSample(
            <<<'CODE_SAMPLE'
#[\OpenApi\Spec\Operation\Get(path: '/pets')]
#[\OpenApi\Spec\Operation\Post(path: '/pets')]
#[\OpenApi\Spec\Response(response: 200, description: 'OK')]
public function pets() {}
CODE_SAMPLE,
            <<<'CODE_SAMPLE'
#[\OpenApi\Spec\Operation\Get(path: '/pets')]
#[\OpenApi\Spec\Operation\Post(path: '/pets')]
#[\OpenApi\Spec\Response(response: 200, description: 'OK', fan: \OpenApi\Spec\Fan::All)]
public function pets() {}
CODE_SAMPLE
        )]);
    }

    public function getNodeTypes(): array
    {
        return [ClassLike::class, ClassMethod::class, Property::class, ClassConst::class];
    }

    public function refactor(Node $node): ?Node
    {
        if (!$node instanceof ClassLike && !$node instanceof ClassMethod && !$node instanceof Property && !$node instanceof ClassConst) {
            return null;
        }

        [$fanned, $outer] = $this->resolveSiblings($this->attributesOf($node->attrGroups));

        if ($node instanceof ClassMethod) {
            foreach ($node->params as $param) {
                [$fannedInner, $inner] = $this->resolveSiblings($this->attributesOf($param->attrGroups));
                array_push($fanned, ...$fannedInner, ...$this->resolveHierarchy($inner, $outer));
            }
        }

        $changed = false;
        foreach ($fanned as $attribute) {
            $changed = $this->addFan($attribute) || $changed;
        }

        return $changed ? $node : null;
    }

    /**
     * @param array<Node\AttributeGroup> $groups
     *
     * @return list<array{Attribute, class-string<AttributeInterface>}> each spec attribute with its spec class
     */
    private function attributesOf(array $groups): array
    {
        $attributes = [];
        foreach ($groups as $group) {
            foreach ($group->attrs as $attribute) {
                $class = $this->specClass($this->getName($attribute->name));
                if ($class !== null) {
                    $attributes[] = [$attribute, $class];
                }
            }
        }

        return $attributes;
    }

    /**
     * The attributes that merge into more than one sibling, and the siblings left unmerged.
     *
     * @param list<array{Attribute, class-string<AttributeInterface>}> $attributes
     *
     * @return array{list<Attribute>, list<array{Attribute, class-string<AttributeInterface>}>}
     */
    private function resolveSiblings(array $attributes): array
    {
        $pending = [];
        foreach ($attributes as $index => [, $class]) {
            if ($this->targets($class)['merge'] !== []) {
                $pending[$index] = true;
            }
        }

        $merged = [];
        $fanned = [];
        $resolve = function (int $index) use ($attributes, &$merged, &$fanned): void {
            $candidates = array_diff_key($attributes, $merged, [$index => true]);
            $matches = $this->matches($attributes[$index][1], $candidates, 'merge');
            if ($matches === []) {
                return;
            }

            $merged[$index] = true;
            if ($this->fansOut($matches)) {
                $fanned[] = $attributes[$index][0];
            }
        };

        while ($pending !== []) {
            $progress = false;
            foreach (array_keys($pending) as $index) {
                if ($this->isMergeTarget($attributes, $pending, $index)) {
                    continue;
                }

                $resolve($index);
                unset($pending[$index]);
                $progress = true;
            }

            if (!$progress) {
                $index = array_key_first($pending);
                $resolve($index);
                unset($pending[$index]);
            }
        }

        return [$fanned, array_values(array_diff_key($attributes, $merged))];
    }

    /**
     * The parameter attributes that more than one of the method's attributes contains.
     *
     * @param list<array{Attribute, class-string<AttributeInterface>}> $inner
     * @param list<array{Attribute, class-string<AttributeInterface>}> $outer
     *
     * @return list<Attribute>
     */
    private function resolveHierarchy(array $inner, array $outer): array
    {
        $fanned = [];
        foreach ($inner as [$attribute, $class]) {
            if ($this->fansOut($this->matches($class, $outer, 'contained'))) {
                $fanned[] = $attribute;
            }
        }

        return $fanned;
    }

    /**
     * @param list<array{Attribute, class-string<AttributeInterface>}> $attributes
     * @param array<int, true>                                         $pending
     */
    private function isMergeTarget(array $attributes, array $pending, int $index): bool
    {
        foreach (array_keys($pending) as $otherIndex) {
            if ($otherIndex === $index) {
                continue;
            }

            foreach (array_keys($this->targets($attributes[$otherIndex][1])['merge']) as $target) {
                if (is_a($attributes[$index][1], $target, true)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * The target types each candidate matches, for those matching any.
     *
     * @param class-string<AttributeInterface>                               $class
     * @param array<int, array{Attribute, class-string<AttributeInterface>}> $candidates
     * @param 'merge'|'contained'                                            $kind
     *
     * @return list<list<string>>
     */
    private function matches(string $class, array $candidates, string $kind): array
    {
        $matches = [];
        foreach ($candidates as [, $candidate]) {
            $types = array_values(array_filter(
                array_keys($this->targets($class)[$kind]),
                static fn (string $type): bool => is_a($candidate, $type, true),
            ));
            if ($types !== []) {
                $matches[] = $types;
            }
        }

        return $matches;
    }

    /**
     * @param list<list<string>> $matches
     */
    private function fansOut(array $matches): bool
    {
        return count($matches) > 1 && array_intersect(...$matches) !== [];
    }

    private function addFan(Attribute $attribute): bool
    {
        foreach ($attribute->args as $arg) {
            if ($arg->name instanceof Identifier && $arg->name->toString() === 'fan') {
                return false;
            }
        }

        $attribute->args[] = new Arg(
            new ClassConstFetch(new FullyQualified(Fan::class), 'All'),
            name: new Identifier('fan'),
        );

        return true;
    }

    /**
     * @return class-string<AttributeInterface>|null
     */
    private function specClass(?string $name): ?string
    {
        if ($name === null) {
            return null;
        }

        self::$classes ??= require __DIR__ . '/../set/classic-to-spec-classes.php';
        $class = self::$classes[$name] ?? $name;

        return is_string($class) && str_starts_with($class, 'OpenApi\Spec\\') && is_subclass_of($class, AttributeInterface::class) ? $class : null;
    }

    /**
     * @param class-string<AttributeInterface> $class
     *
     * @return array{merge: array<string, string>, contained: array<string, string>}
     */
    private function targets(string $class): array
    {
        if (!isset(self::$targets[$class])) {
            // both are fixed maps, so an instance without its constructor answers them
            $attribute = (new \ReflectionClass($class))->newInstanceWithoutConstructor();
            self::$targets[$class] = ['merge' => $attribute->merge(), 'contained' => $attribute->contained()];
        }

        return self::$targets[$class];
    }
}
