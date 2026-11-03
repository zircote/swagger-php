<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Contracts;

use OpenApi\Assembler;
use OpenApi\Specification\ComponentKey;

/**
 * A resolver that is asked about a key, so it can describe a class for a key with qualifiers,
 * for example one per serialization group.
 *
 * A class name is the plain key, so `resolve()` is `resolveKey()` with the class alone.
 * `Resolver\Concerns\KeyResolverTrait` supplies it.
 */
interface KeyResolverInterface extends ResolverInterface
{
    /**
     * Attempt to resolve a key that is referenced by the specification but has no matching component.
     *
     * A component added for a key with qualifiers carries that key, set with `setComponentKey()`,
     * so the reference finds it.
     *
     * @return bool <code>true</code> if the key was handled; stops the resolver chain for this key
     */
    public function resolveKey(ComponentKey $key, Assembler $assembler): bool;
}
