<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Analysers;

use PhpParser\Error;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Enum_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\Node\Stmt\TraitUse;
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
        foreach ($stmts as $stmt) {
            //echo 'top: ' . get_class($stmt), PHP_EOL;
            if ($stmt instanceof Namespace_) {
                $namespace = (string)$stmt->name;

                $uses = [];
                $resolve = function(string $name) use ($namespace, &$uses) {
                    if (array_key_exists($name, $uses)) {
                        return $uses[$name];
                    }

                    return $namespace.'\\'.$name;
                };
                foreach ($stmt->stmts as $subStmt) {
                    //echo 'sub: ' . get_class($subStmt), PHP_EOL;
                    switch (get_class($subStmt)) {
                        case Use_::class:
                            $uses += $this->collect_uses($subStmt);
                            break;
                        case Class_::class:
                            $result += $this->collect_class($subStmt, $uses, $resolve);
                            break;
                        case Interface_::class:
                            $result += $this->collect_interface($subStmt, $uses, $resolve);
                            break;
                        case Trait_::class:
                            $result += $this->collect_trait($subStmt, $uses, $resolve);
                            break;
                        case Enum_::class:
                            $result += $this->collect_enum($subStmt, $uses, $resolve);
                            break;
                    }
                }
            }
        }

        return $result;
    }

    protected function collect_uses(Use_ $stmt): array
    {
        $uses = [];

        foreach ($stmt->uses as $use) {
            $uses[(string)$use->getAlias()] = (string)$use->name;
        }

        return $uses;
    }

    protected function collect_classlike(ClassLike $stmt, array $details, callable $resolve):
    array
    {
        if (!array_key_exists('properties', $details)) {
            $details['properties'] = [];
        }
        $details['properties'] = array_merge(array_map(function (Property $p) {
            return (string)$p->props[0]->name;
        }, $stmt->getProperties()), $details['properties']);
        $details['methods'] = array_map(function (ClassMethod $m) {
            return (string)$m->name;
        }, $stmt->getMethods());
        $details['traits'] = array_map(function (TraitUse $traitUse) use ($resolve) {
            return $resolve((string)$traitUse->traits[0]);
        }, $stmt->getTraitUses());

        return [
            $resolve($stmt->name->name) => $details,
        ];
    }

    protected function collect_class(Class_ $stmt, array $uses, callable $resolve): array
    {
        $details = [];

        $details['uses'] = $uses;
        $details['interfaces'] = array_map(function (Name $name) use ($resolve) {
            return $resolve((string)$name);
        }, $stmt->implements);
        $details['enums'] = [];

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

    protected function collect_interface(Interface_ $stmt, array $uses, callable $resolve): array
    {
        $details = [];

        $details['uses'] = $uses;
        $details['interfaces'] = array_map(function (Name $name) use ($resolve) {
            return $resolve((string)$name);
        }, $stmt->extends);
        $details['enums'] = [];

        return $this->collect_classlike($stmt, $details, $resolve);
    }

    protected function collect_trait(Trait_ $stmt, array $uses, callable $resolve): array
    {
        $details = [];

        $details['uses'] = $uses;
        $details['interfaces'] = [];
        $details['enums'] = [];

        return $this->collect_classlike($stmt, $details, $resolve);
    }

    protected function collect_enum(Enum_ $stmt, array $uses, callable $resolve): array
    {
        $details = [];

        $details['uses'] = $uses;
        $details['interfaces'] = [];
        $details['traits'] = [];
        $details['enums'] = [];

        return $this->collect_classlike($stmt, $details, $resolve);
    }
}
