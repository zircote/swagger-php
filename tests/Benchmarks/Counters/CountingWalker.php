<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Benchmarks\Counters;

use OpenApi\Specification\Walker;

/**
 * Reports each walk over the whole specification to the specification it walks.
 */
class CountingWalker extends Walker
{
    public function __construct(private readonly CountingSpecification $counting)
    {
        parent::__construct($counting);
    }

    public function visit(string|array $visitee, callable $visitor): void
    {
        $this->counting->countWalk();

        parent::visit($visitee, $visitor);
    }
}
