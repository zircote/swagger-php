<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Benchmarks\Timing;

/**
 * Durations in milliseconds, by phase, in the order the phases ran.
 */
final class Timings
{
    /** @var array<string, float> */
    private array $phases = [];

    public function reset(): void
    {
        $this->phases = [];
    }

    public function add(string $phase, float $milliseconds): void
    {
        $this->phases[$phase] = ($this->phases[$phase] ?? 0.0) + $milliseconds;
    }

    /**
     * Runs `$work`, adds its duration to `$phase`, and returns its result.
     *
     * @template T
     *
     * @param callable(): T $work
     *
     * @return T
     */
    public function time(string $phase, callable $work): mixed
    {
        $start = hrtime(true);
        try {
            return $work();
        } finally {
            $this->add($phase, (hrtime(true) - $start) / 1e6);
        }
    }

    /**
     * @return array<string, float>
     */
    public function all(): array
    {
        return $this->phases;
    }
}
