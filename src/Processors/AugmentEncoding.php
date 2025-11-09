<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors;

use OpenApi\Analysis;
use OpenApi\GeneratorAwareInterface;
use OpenApi\GeneratorAwareTrait;

/**
 * Augment media type / property encodings.
 */
class AugmentEncoding implements GeneratorAwareInterface
{
    use GeneratorAwareTrait;

    public function __invoke(Analysis $analysis): void
    {
    }
}
