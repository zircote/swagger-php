<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Benchmarks\Fixtures;

/**
 * The size and shape of a generated API.
 *
 * Every schema is a class with typed properties and an inline object nested `$depth` levels
 * deep. The used schemas reference each other in a ring and each has an operation; the unused
 * ones are referenced by nothing, for `Cleanup` to remove. `$noise` adds classes without
 * attributes around the API, which a scan reads and a reflector-seeded build never touches.
 */
final readonly class Shape
{
    public function __construct(
        public int $schemas = 100,
        public int $depth = 2,
        public float $unusedRatio = 0.4,
        public int $noise = 0,
        public int $perController = 10,
    ) {
        if ($schemas < 1 || $depth < 0 || $unusedRatio < 0 || $unusedRatio >= 1 || $noise < 0 || $perController < 1) {
            throw new \InvalidArgumentException('A shape needs at least one schema, and non-negative settings with an unused ratio below 1');
        }
    }

    public function used(): int
    {
        return max(1, (int) round($this->schemas * (1 - $this->unusedRatio)));
    }

    public function controllers(): int
    {
        return (int) ceil($this->used() / $this->perController);
    }

    /**
     * A name for the shape, safe in a path and a namespace.
     */
    public function key(): string
    {
        return sprintf('S%dD%dU%dN%dP%d', $this->schemas, $this->depth, (int) round($this->unusedRatio * 100), $this->noise, $this->perController);
    }
}
