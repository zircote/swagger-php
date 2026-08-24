<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Resolver;

use OpenApi\Assembler;
use OpenApi\Contracts\AttributeInterface;
use OpenApi\Contracts\ResolverInterface;

/**
 * Resolves missing components by collecting the referenced class with the assembler in use.
 *
 * This makes listing all related classes as builder sources optional; adding a single controller
 * is enough as long as everything it references (directly or transitively) carries spec attributes.
 *
 * A FQCN is considered resolved if the specification knows it once collected. Classes without any
 * spec attributes are left to the next resolver in the chain.
 */
class Reflection implements ResolverInterface
{
    public function resolve(string $fqcn, Assembler $assembler): bool
    {
        if (!class_exists($fqcn) && !interface_exists($fqcn) && !enum_exists($fqcn)) {
            return false;
        }

        $assembler->collect(new \ReflectionClass($fqcn));

        return $assembler->getSpecification()->buildComponentIndex()->find($fqcn) instanceof AttributeInterface;
    }
}
