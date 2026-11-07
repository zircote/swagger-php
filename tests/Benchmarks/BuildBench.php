<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Benchmarks;

use OpenApi\Builder\Mode;
use OpenApi\Tests\Benchmarks\Fixtures\Fixture;
use OpenApi\Tests\Benchmarks\Fixtures\FixtureWriter;
use OpenApi\Tests\Benchmarks\Fixtures\Flavour;
use OpenApi\Tests\Benchmarks\Fixtures\Shape;
use PhpBench\Attributes as Bench;

/**
 * A whole build, from source to document, in each mode.
 *
 * Classic and hybrid scan the directory. Spec also builds seeded with the entry classes, the way
 * a reflector-seeded build reaches only what the API references.
 */
#[Bench\BeforeMethods('setUp')]
#[Bench\Warmup(1)]
#[Bench\Revs(3)]
#[Bench\Iterations(5)]
#[Bench\OutputTimeUnit('milliseconds', precision: 1)]
#[Bench\ParamProviders(['shapes', 'variants', 'cleanup'])]
class BuildBench
{
    private Fixture $fixture;

    /**
     * @param array{schemas: int, depth: int, flavour: string, mode: string, scan: bool, cleanup: bool} $params
     */
    public function setUp(array $params): void
    {
        $this->fixture = (new FixtureWriter())->write(
            new Shape(schemas: $params['schemas'], depth: $params['depth']),
            Flavour::from($params['flavour']),
        );
    }

    /**
     * @param array{schemas: int, depth: int, flavour: string, mode: string, scan: bool, cleanup: bool} $params
     */
    public function benchBuild(array $params): void
    {
        $this->fixture->build(Mode::from($params['mode']), $params['cleanup'], $params['scan']);
    }

    public function shapes(): \Generator
    {
        yield 'S100D2' => ['schemas' => 100, 'depth' => 2];
        yield 'S100D6' => ['schemas' => 100, 'depth' => 6];
        yield 'S500D2' => ['schemas' => 500, 'depth' => 2];
    }

    public function variants(): \Generator
    {
        yield 'classic' => ['flavour' => Flavour::Classic->value, 'mode' => Mode::CLASSIC->value, 'scan' => true];
        yield 'hybrid' => ['flavour' => Flavour::Classic->value, 'mode' => Mode::HYBRID->value, 'scan' => true];
        yield 'spec' => ['flavour' => Flavour::Spec->value, 'mode' => Mode::SPEC->value, 'scan' => true];
        yield 'spec seeded' => ['flavour' => Flavour::Spec->value, 'mode' => Mode::SPEC->value, 'scan' => false];
    }

    public function cleanup(): \Generator
    {
        yield 'cleanup' => ['cleanup' => true];
        yield 'no cleanup' => ['cleanup' => false];
    }
}
