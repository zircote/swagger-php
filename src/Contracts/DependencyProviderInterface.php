<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Contracts;

use OpenApi\Specification;

interface DependencyProviderInterface
{
    /**
     * Find FQCNs referenced by integration-specific metadata in the specification.
     *
     * Called on each discovery pass, after contribution hooks and before augmentation. Newly
     * collected components are visible on the next pass. Providers read the specification
     * without changing it; the resolver filters existing components and duplicate names, then
     * offers the remaining names to its ResolverInterface chain with the active assembler.
     *
     * @return iterable<string> FQCNs to resolve, including names handled by a custom resolver
     */
    public function dependencies(Specification $specification): iterable;
}
