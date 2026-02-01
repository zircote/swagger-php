<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace SortComponentsProcessor;

use OpenApi\Analysis;

/**
 * Sorts components so they appear in alphabetical order in the generated specs.
 */
class SortComponents
{
    public function __invoke(Analysis $analysis): void
    {
        if (is_object($analysis->openapi->components) && is_iterable($analysis->openapi->components->schemas)) {
            usort($analysis->openapi->components->schemas, fn ($a, $b): int => strcmp((string) $a->schema, (string) $b->schema));
        }
    }
}
