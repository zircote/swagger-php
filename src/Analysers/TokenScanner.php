<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Analysers;

use PhpParser\Error;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\Enum_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\Node\Stmt\Use_;
use PhpParser\ParserFactory;

/**
 * High level, PHP token based, scanner.
 */
class TokenScanner
{
    /**
     * Scan file for all classes, interfaces and traits.
     *
     * @return string[][] File details
     */
    public function scanFile(string $filename): array
    {
        $parser = (new ParserFactory())->createForNewestSupportedVersion();
        try {
            $stmts = $parser->parse(file_get_contents($filename));
        } catch (Error $e) {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }

        $result = [];
        $result += $this->collect_stmts($stmts, '');
        foreach ($stmts as $stmt) {
            if ($stmt instanceof Namespace_) {
                $namespace = (string) $stmt->name;

                $result += $this->collect_stmts($stmt->stmts, $namespace);
            }
        }

        return $result;
    }

    protected function collect_stmts(array $stmts, string $namespace): array
    {
        /** @var array $uses */
        $uses = [];
        $resolve = function (string $name) use ($namespace, &$uses) {
            if (array_key_exists($name, $uses)) {
                return $uses[$name];
            }

            return $namespace . '\\' . $name;
        };
        $details = function () use (&$uses) {
            return [
                'uses' => $uses,
                'interfaces' => [],
                'traits' => [],
                'enums' => [],
                'methods' => [],
                'properties' => [],
            ];
        };
        $result = [];
        foreach ($stmts as $stmt) {
            switch (get_class($stmt)) {
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

    protected function collect_uses(Use_ $stmt): array
    {
        $uses = [];

        foreach ($stmt->uses as $use) {
            $uses[(string) $use->getAlias()] = (string) $use->name;
        }

        return $uses;
    }

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

        return [
            $resolve($stmt->name->name) => $details,
        ];
    }

    protected function collect_class(Class_ $stmt, array $details, callable $resolve): array
    {
        foreach ($stmt->implements as $implement) {
            $details['interfaces'][] = $resolve((string) $implement);
        }

        // promoted properties
        if ($ctor = $stmt->getMethod('__construct')) {
            foreach ($ctor->getParams() as $param) {
                if ($param->flags) {
                    $details['properties'][] = $param->var->name;
                }
            }
        }

        return $this->collect_classlike($stmt, $details, $resolve);
    }

    protected function collect_interface(Interface_ $stmt, array $details, callable $resolve): array
    {
        foreach ($stmt->extends as $extend) {
            $details['interfaces'][] = $resolve((string) $extend);
        }

        return $this->collect_classlike($stmt, $details, $resolve);
    }
}
