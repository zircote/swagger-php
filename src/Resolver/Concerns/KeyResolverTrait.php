<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Resolver\Concerns;

use OpenApi\Assembler;
use OpenApi\Specification\ComponentKey;

/**
 * Resolves a class name as its plain key, for a `KeyResolverInterface`.
 *
 * `KeyResolverInterface` extends `ResolverInterface`, so `resolve()` is still part of the contract,
 * and code written against `ResolverInterface` still calls it. This answers it with `resolveKey()`,
 * so a key-aware resolver writes one method and stays usable wherever a `ResolverInterface` is
 * expected.
 */
trait KeyResolverTrait
{
    public function resolve(string $fqcn, Assembler $assembler): bool
    {
        return $this->resolveKey(new ComponentKey($fqcn), $assembler);
    }
}
