<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace SortComponentsAugmenter;

use OpenApi\Augmenter\Group;
use OpenApi\Specification;
use OpenApi\Utils\PipeInterface;

/**
 * Sorts schema components, so they appear in alphabetical order in the generated document.
 */
class SortComponents implements PipeInterface
{
    /**
     * @param Specification $payload
     */
    public function __invoke(mixed $payload): mixed
    {
        usort($payload->schemas, fn ($a, $b): int => strcmp((string) $a->component, (string) $b->component));

        return $payload;
    }

    public function group(): string|\BackedEnum
    {
        return Group::Augment;
    }
}
