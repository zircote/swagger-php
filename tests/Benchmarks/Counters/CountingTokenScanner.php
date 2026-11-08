<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Benchmarks\Counters;

use OpenApi\Utils\TokenScanner;

/**
 * Counts the files this scanner has parsed. It caches each one, so the count is of distinct files,
 * and a file parsed by another scanner does not show.
 */
class CountingTokenScanner extends TokenScanner
{
    public function parsed(): int
    {
        return count($this->cache);
    }
}
