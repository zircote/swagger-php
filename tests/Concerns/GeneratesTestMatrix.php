<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Concerns;

use OpenApi\Builder\Mode;

trait GeneratesTestMatrix
{
    protected static function versions(): array
    {
        return ['3.0.0', '3.1.0', '3.2.0'];
    }

    protected static function modes(): array
    {
        return [Mode::CLASSIC, Mode::HYBRID, Mode::SPEC];
    }

    protected static function phpVersion(): string
    {
        return PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION;
    }

    protected static function mostSpecific(array $candidates): ?string
    {
        foreach ($candidates as $candidate) {
            if (file_exists($candidate)) {
                return $candidate;
            }
        }

        return null;
    }

    /**
     * Generate cross-product of axes, filtered by exclusion rules.
     *
     * @param array<string, array> $axes       Named axes with their values
     * @param list<callable>       $exclusions Callables that return true to exclude a combination
     *
     * @return iterable<array<string, mixed>>
     */
    protected static function matrixCombinations(array $axes, array $exclusions = []): iterable
    {
        $keys = array_keys($axes);
        $values = array_values($axes);

        foreach (self::cartesian($values) as $combo) {
            $named = array_combine($keys, $combo);

            $excluded = false;
            foreach ($exclusions as $exclusion) {
                if ($exclusion($named)) {
                    $excluded = true;
                    break;
                }
            }

            if (!$excluded) {
                yield $named;
            }
        }
    }

    /**
     * @return iterable<string, string> name => path
     */
    protected static function discoverFixtures(string $pattern, array $skipPrefixes = ['Abstract']): iterable
    {
        foreach (glob($pattern) as $file) {
            $name = pathinfo($file, PATHINFO_FILENAME);

            foreach ($skipPrefixes as $prefix) {
                if (str_starts_with($name, $prefix)) {
                    continue 2;
                }
            }

            yield $name => $file;
        }
    }

    protected static function matrixKey(array $parts): string
    {
        return implode('-', array_filter($parts));
    }

    private static function cartesian(array $arrays): iterable
    {
        if ($arrays === []) {
            yield [];

            return;
        }

        $first = array_shift($arrays);
        foreach ($first as $value) {
            foreach (self::cartesian($arrays) as $rest) {
                yield [$value, ...$rest];
            }
        }
    }
}
