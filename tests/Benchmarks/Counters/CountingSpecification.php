<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Benchmarks\Counters;

use OpenApi\Specification;
use OpenApi\Specification\Walker;

/**
 * A specification that counts the walks over it.
 */
class CountingSpecification extends Specification
{
    // private, so code reading the specification's properties never sees it
    private int $walks = 0;

    public function getWalker(): Walker
    {
        return new CountingWalker($this);
    }

    public function countWalk(): void
    {
        ++$this->walks;
    }

    public function walks(): int
    {
        return $this->walks;
    }
}
