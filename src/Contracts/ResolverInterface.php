<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Contracts;

use OpenApi\Assembler;

interface ResolverInterface
{
    /**
     * Attempt to resolve a FQCN that is referenced by the specification but has no matching component.
     *
     * The assembler is the one used to assemble the specification being built; collecting a reflector
     * with it adds the result straight into that specification. Resolvers that assemble differently
     * may use their own assembler and add to `$assembler->getSpecification()` directly.
     *
     * @return bool <code>true</code> if the FQCN was handled; stops the resolver chain for this FQCN
     */
    public function resolve(string $fqcn, Assembler $assembler): bool;
}
