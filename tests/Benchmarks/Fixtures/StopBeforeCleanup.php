<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Benchmarks\Fixtures;

use OpenApi\Specification;

/**
 * Carries the specification out of a build stopped right before `Augmenter\Cleanup`.
 */
final class StopBeforeCleanup extends \RuntimeException
{
    public function __construct(public readonly Specification $specification)
    {
        parent::__construct('Stopped before Augmenter\Cleanup');
    }
}
