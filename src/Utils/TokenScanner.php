<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Utils;

use OpenApi\Contracts\SourceInspectorInterface;
use PhpParser\Error;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Enum_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\Node\Stmt\Use_;
use PhpParser\ParserFactory;

/**
 * High-level, PHP-token-based, scanner.
 *
 * Answers {@see SourceInspectorInterface} by parsing the file a class is declared in. A class
 * without a readable file, internal or from `eval()`, owns every member declared on it, and uses
 * no trait as far as the scanner can tell.
 *
 * @deprecated since 7.6, removed in 8.0 - use `SourceInspector`
 *
 * @phpstan-type ScannerDetails array{
 *     uses: array<string, class-string>,
 *     interfaces: list<class-string>,
 *     traits: list<class-string>,
 *     enums: list<class-string>,
 *     methods: list<string>,
 *     properties: list<string>,
 *     consts: list<string>,
 * }
 */
class TokenScanner implements SourceInspectorInterface
{
    /** @var array<string, array<class-string, ScannerDetails>> */
    protected array $cache = [];

    /**
     * Scan a given file for all classes, interfaces, and traits.
     *
     * Results are cached by filename — repeated calls with the same path return the cached result.
     *
     * @return array<class-string, ScannerDetails>
     */
    public function scanFile(string $filename): array
    {
        if (isset($this->cache[$filename])) {
            return $this->cache[$filename];
        }

        $code = is_file($filename) && is_readable($filename) ? file_get_contents($filename) : false;
        if (false === $code) {
            throw new \RuntimeException("Unable to read {$filename}");
        }

        $parser = (new ParserFactory())->createForNewestSupportedVersion();
        try {
            $stmts = $parser->parse($code) ?? [];
        } catch (Error $error) {
            throw new \RuntimeException($error->getMessage(), $error->getCode(), $error);
        }

        $result = [];
        $result += $this->collect_stmts($stmts, '');
        foreach ($stmts as $stmt) {
            if ($stmt instanceof Namespace_) {
                $namespace = (string) $stmt->name;

                $result += $this->collect_stmts($stmt->stmts, $namespace);
            }
        }

        $this->cache[$filename] = $result;

        return $result;
    }

    /**
     * Look up scan details for a specific FQDN.
     *
     * Scans the file on demand if not already cached.
     *
     * @param \ReflectionClass<object> $class
     *
     * @return ScannerDetails|null
     */
    public function detailsFor(\ReflectionClass $class): ?array
    {
        $filename = $class->getFileName();
        // internal classes have no file, and a class from `eval()` names one that does not exist;
        // a scanned file is answered from the cache without touching the filesystem again
        if ($filename === false || (!isset($this->cache[$filename]) && !is_file($filename))) {
            return null;
        }

        return $this->scanFile($filename)[$class->getName()] ?? null;
    }

    public function classesIn(string $filename): array
    {
        return array_keys($this->scanFile($filename));
    }

    public function declares(\ReflectionClass $class, \ReflectionMethod|\ReflectionProperty|\ReflectionClassConstant $member): bool
    {
        if ($member->getDeclaringClass()->getName() !== $class->getName()) {
            return false;
        }

        // reflection reports a trait's member as declared by the class using it
        $details = $this->detailsFor($class);
        $declared = match (true) {
            $details === null => null,
            $member instanceof \ReflectionMethod => $details['methods'],
            $member instanceof \ReflectionProperty => $details['properties'],
            default => $details['consts'],
        };

        return $declared === null || in_array($member->getName(), $declared, true);
    }

    public function directTraits(\ReflectionClass $class): array
    {
        $traits = [];
        foreach ($this->detailsFor($class)['traits'] ?? [] as $name) {
            if (trait_exists($name) || class_exists($name)) {
                $traits[] = new \ReflectionClass($name);
            }
        }

        return $traits;
    }

    /**
     * @param array<Stmt> $stmts
     *
     * @return array<class-string, ScannerDetails>
     */
    protected function collect_stmts(array $stmts, string $namespace): array
    {
        /** @var array<string, class-string> $uses */
        $uses = [];
        /**
         * @return class-string
         */
        $resolve = static function (string $name) use ($namespace, &$uses): string {
            if (array_key_exists($name, $uses)) {
                return $uses[$name];
            }

            /** @var class-string $fqdn */
            $fqdn = $namespace . '\\' . $name;

            return $fqdn;
        };
        $details = static function () use (&$uses): array {
            return [
                'uses' => $uses,
                'interfaces' => [],
                'traits' => [],
                'enums' => [],
                'methods' => [],
                'properties' => [],
                'consts' => [],
            ];
        };
        $result = [];
        foreach ($stmts as $stmt) {
            switch ($stmt::class) {
                case Use_::class:
                    $uses += $this->collect_uses($stmt);
                    break;
                case Class_::class:
                    $result += $this->collect_class($stmt, $details(), $resolve);
                    break;
                case Interface_::class:
                    $result += $this->collect_interface($stmt, $details(), $resolve);
                    break;
                case Trait_::class:
                case Enum_::class:
                    $result += $this->collect_classlike($stmt, $details(), $resolve);
                    break;
            }
        }

        return $result;
    }

    /**
     * @return array<string, class-string>
     */
    protected function collect_uses(Use_ $stmt): array
    {
        /** @var array<string, class-string> $uses */
        $uses = [];

        foreach ($stmt->uses as $use) {
            /** @var class-string $name */
            $name = (string) $use->name;
            $uses[(string) $use->getAlias()] = $name;
        }

        return $uses;
    }

    /**
     * @param ScannerDetails                 $details
     * @param callable(string): class-string $resolve
     *
     * @return array<class-string, ScannerDetails>
     */
    protected function collect_classlike(ClassLike $stmt, array $details, callable $resolve): array
    {
        foreach ($stmt->getProperties() as $properties) {
            foreach ($properties->props as $prop) {
                $details['properties'][] = (string) $prop->name;
            }
        }

        foreach ($stmt->getMethods() as $method) {
            $details['methods'][] = (string) $method->name;
        }

        foreach ($stmt->getTraitUses() as $traitUse) {
            foreach ($traitUse->traits as $trait) {
                $details['traits'][] = $resolve((string) $trait);
            }
        }

        foreach ($stmt->getConstants() as $const) {
            foreach ($const->consts as $c) {
                $details['consts'][] = (string) $c->name;
            }
        }

        return [
            $resolve($stmt->name->name) => $details,
        ];
    }

    /**
     * @param ScannerDetails                 $details
     * @param callable(string): class-string $resolve
     *
     * @return array<class-string, ScannerDetails>
     */
    protected function collect_class(Class_ $stmt, array $details, callable $resolve): array
    {
        foreach ($stmt->implements as $implement) {
            $details['interfaces'][] = $resolve((string) $implement);
        }

        // promoted properties
        if (($ctor = $stmt->getMethod('__construct')) instanceof ClassMethod) {
            foreach ($ctor->getParams() as $param) {
                if ($param->flags && $param->var instanceof Variable && is_string($param->var->name)) {
                    $details['properties'][] = $param->var->name;
                }
            }
        }

        return $this->collect_classlike($stmt, $details, $resolve);
    }

    /**
     * @param ScannerDetails                 $details
     * @param callable(string): class-string $resolve
     *
     * @return array<class-string, ScannerDetails>
     */
    protected function collect_interface(Interface_ $stmt, array $details, callable $resolve): array
    {
        foreach ($stmt->extends as $extend) {
            $details['interfaces'][] = $resolve((string) $extend);
        }

        return $this->collect_classlike($stmt, $details, $resolve);
    }
}
