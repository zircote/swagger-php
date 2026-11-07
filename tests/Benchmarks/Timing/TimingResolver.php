<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Benchmarks\Timing;

use OpenApi\Assembler;
use OpenApi\Resolver;

/**
 * Times the resolver's passes, together.
 */
class TimingResolver extends Resolver
{
    public function __construct(protected Timings $timings)
    {
        parent::__construct();
    }

    public function resolve(Assembler $assembler): void
    {
        $this->timings->time('resolve', fn () => parent::resolve($assembler));
    }
}
