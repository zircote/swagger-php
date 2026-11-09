<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Benchmarks;

use OpenApi\Builder\Mode;
use OpenApi\Tests\Benchmarks\Counters\CountingBuilder;
use OpenApi\Tests\Benchmarks\Fixtures\FixtureWriter;
use OpenApi\Tests\Benchmarks\Fixtures\Flavour;
use OpenApi\Tests\Benchmarks\Fixtures\Shape;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Pins how much work a build does, as counts rather than time, so a change that makes a build do
 * more fails on any machine. Each count follows from the shape. One that moves means the build
 * does more than it needs to, or the expectation here moves with a reason.
 *
 * Walks over the whole document do not follow from the shape: each augmenter, resolver pass or
 * compiler check that walks does so once, whatever the size. A cleanup that removes something
 * walks again, and so does a second resolver pass. A scan and a seeded build come to the same
 * count for that reason: the scan's cleanup removes the unused schemas, and the seeded build,
 * which never reached them, makes a second resolver pass instead.
 */
final class CountersTest extends TestCase
{
    /**
     * @return iterable<string, array{Shape}>
     */
    public static function shapesProvider(): iterable
    {
        yield 'flat' => [new Shape(schemas: 3, depth: 0, perController: 2)];
        yield 'nested' => [new Shape(schemas: 8, depth: 3, unusedRatio: 0.25, noise: 2, perController: 3)];
        yield 'wide' => [new Shape(schemas: 40, depth: 1, noise: 10, perController: 4)];
    }

    /**
     * @return iterable<string, array{Shape, Flavour, Mode}>
     */
    public static function scansProvider(): iterable
    {
        foreach (self::shapesProvider() as $name => [$shape]) {
            yield "{$name}, spec" => [$shape, Flavour::Spec, Mode::SPEC];
            yield "{$name}, hybrid" => [$shape, Flavour::Classic, Mode::HYBRID];
        }
    }

    /**
     * A scan reads every file once for its classes, and the resolver finds nothing left to resolve.
     *
     * Only the attribute factory's inspector is counted. Hybrid's classic analysis parses each file
     * with scanners of its own, which this does not see.
     */
    #[DataProvider('scansProvider')]
    public function testAScanReadsEveryFileOnce(Shape $shape, Flavour $flavour, Mode $mode): void
    {
        $builder = new CountingBuilder();
        (new FixtureWriter())->write($shape, $flavour)->build($mode, builder: $builder);

        $this->assertSame([
            'read' => 1 + $shape->schemas + $shape->controllers() + $shape->noise,
            'passes' => 1,
            'unresolved' => 0,
            'walks' => 32,
        ], $builder->counts());
    }

    /**
     * Seeded with the controllers, a build reads no file: reflection tells each class's own members
     * from inherited ones. The first resolver pass finds every used model, since each has an
     * operation, and the second finds nothing more.
     */
    #[DataProvider('shapesProvider')]
    public function testASeededBuildReadsNoFile(Shape $shape): void
    {
        $builder = new CountingBuilder();
        (new FixtureWriter())->write($shape, Flavour::Spec)->build(Mode::SPEC, scan: false, builder: $builder);

        $this->assertSame([
            'read' => 0,
            'passes' => 2,
            'unresolved' => $shape->used(),
            'walks' => 32,
        ], $builder->counts());
    }
}
