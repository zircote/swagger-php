<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Resolver;

use OpenApi\Assembler;
use OpenApi\Contracts\KeyResolverInterface;
use OpenApi\Resolver\Concerns\KeyResolverTrait;
use OpenApi\Specification\ComponentKey;

/**
 * Resolves missing components by collecting the referenced class with the assembler in use.
 *
 * This makes listing all related classes as builder sources optional; adding a single controller
 * is enough as long as everything it references (directly or transitively) carries spec attributes.
 *
 * A FQCN is considered resolved if the specification knows it once collected. Classes without any
 * spec attributes are left to the next resolver in the chain. So is a key with qualifiers: the
 * class's own attributes describe it once, as the plain key.
 */
class Reflection implements KeyResolverInterface
{
    use KeyResolverTrait;

    public function resolveKey(ComponentKey $key, Assembler $assembler): bool
    {
        $fqcn = $key->class;
        if (!$key->isPlain() || (!class_exists($fqcn) && !interface_exists($fqcn) && !enum_exists($fqcn))) {
            return false;
        }

        $assembler->collect(new \ReflectionClass($fqcn));

        return $assembler->getSpecification()->buildComponentIndex()->hasKey($key);
    }
}
