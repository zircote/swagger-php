<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\PHP;

/**
 * Readonly classes (8.2), intersection types (8.1), disjunctive normal form types (8.2)
 * and the standalone `null`/`true`/`false` types (8.2).
 */
readonly class ReadonlyAndUnionTypes
{
    public (FirstInterface&SecondInterface)|null $dnf;

    public FirstInterface&SecondInterface $intersection;

    public null $alwaysNull;

    public function __construct(
        public true $alwaysTrue = true,
        public false|int $falseOrInt = false,
    ) {
        $this->dnf = null;
        $this->intersection = new class () implements FirstInterface, SecondInterface {};
        $this->alwaysNull = null;
    }

    public function never(): never
    {
        throw new \RuntimeException();
    }
}
