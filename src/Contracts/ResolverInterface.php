<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Contracts;

use OpenApi\Specification;

interface ResolverInterface
{
    public function resolve(string $fqcn, Specification $specification): bool;
}
