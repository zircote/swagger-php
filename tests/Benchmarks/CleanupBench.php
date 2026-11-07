<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Benchmarks;

use OpenApi\Augmenter\Cleanup;
use OpenApi\Specification;
use OpenApi\Tests\Benchmarks\Fixtures\FixtureWriter;
use OpenApi\Tests\Benchmarks\Fixtures\Flavour;
use OpenApi\Tests\Benchmarks\Fixtures\Shape;
use PhpBench\Attributes as Bench;

/**
 * `Augmenter\Cleanup` on its own, on the specification a spec build hands it.
 *
 * Cleanup removes what it does not need from the specification it is given, so each iteration
 * builds a fresh one and runs it once. A clone would not do: it is shallow, and the attributes
 * the first run touched would be shared with the next.
 */
#[Bench\BeforeMethods('setUp')]
#[Bench\Revs(1)]
#[Bench\Iterations(10)]
#[Bench\OutputTimeUnit('milliseconds', precision: 2)]
#[Bench\ParamProviders(['shapes'])]
class CleanupBench
{
    private Specification $specification;

    /**
     * @param array{schemas: int, depth: int, unused: float} $params
     */
    public function setUp(array $params): void
    {
        $this->specification = (new FixtureWriter())
            ->write(new Shape(schemas: $params['schemas'], depth: $params['depth'], unusedRatio: $params['unused']), Flavour::Spec)
            ->specificationBeforeCleanup();
    }

    public function benchCleanup(): void
    {
        (new Cleanup())($this->specification);
    }

    /**
     * Breadth and depth, each grown on its own from the same starting shape.
     */
    public function shapes(): \Generator
    {
        foreach ([100, 500, 2000] as $schemas) {
            yield "S{$schemas}D2" => ['schemas' => $schemas, 'depth' => 2, 'unused' => 0.4];
        }
        foreach ([0, 6] as $depth) {
            yield "S100D{$depth}" => ['schemas' => 100, 'depth' => $depth, 'unused' => 0.4];
        }
        yield 'S500D2 none unused' => ['schemas' => 500, 'depth' => 2, 'unused' => 0.0];
    }
}
