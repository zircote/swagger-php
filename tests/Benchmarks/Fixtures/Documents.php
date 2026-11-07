<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Benchmarks\Fixtures;

/**
 * Compares documents as data.
 *
 * The modes order keys within a schema differently, and a build seeded with reflectors adds
 * components in the order the resolver reaches them. Neither changes what the document says, so
 * maps are compared with their keys sorted. Lists keep their order.
 */
final class Documents
{
    /**
     * @param array<mixed> $document
     *
     * @return array<mixed>
     */
    public static function normalise(array $document): array
    {
        $normalised = array_map(static fn (mixed $value): mixed => is_array($value) ? self::normalise($value) : $value, $document);

        if (!array_is_list($normalised)) {
            ksort($normalised);
        }

        return $normalised;
    }
}
