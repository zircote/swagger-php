<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Concerns;

trait UsesFixtures
{
    public static function fixture(string $file): ?string
    {
        $fixtures = static::fixtures([$file]);

        return $fixtures !== [] ? $fixtures[0] : null;
    }

    /**
     * Resolve fixture filenames.
     *
     * @return array resolved filenames for loading scanning etc
     */
    public static function fixtures(array $files): array
    {
        return array_map(fn (string $file): string => dirname(__DIR__) . '/Fixtures/' . $file, $files);
    }
}
