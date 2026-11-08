<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Benchmarks\Counters;

use OpenApi\Resolver;
use OpenApi\Specification;
use OpenApi\Utils\AttributeFactory;

/**
 * Counts the passes the resolver makes over the specification.
 */
class CountingResolver extends Resolver
{
    public int $passes = 0;

    public int $unresolved = 0;

    protected function findUnresolved(Specification $specification, AttributeFactory $attributeFactory): array
    {
        ++$this->passes;

        $unresolved = parent::findUnresolved($specification, $attributeFactory);
        $this->unresolved += count($unresolved);

        return $unresolved;
    }
}
