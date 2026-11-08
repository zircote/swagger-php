<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Benchmarks\Counters;

use OpenApi\Utils\SourceInspector;

/**
 * Counts the files this inspector has read.
 */
class CountingSourceInspector extends SourceInspector
{
    public int $read = 0;

    protected function tokens(string $filename): array
    {
        ++$this->read;

        return parent::tokens($filename);
    }
}
