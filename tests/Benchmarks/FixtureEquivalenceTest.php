<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Benchmarks;

use OpenApi\Builder\Mode;
use OpenApi\Tests\Benchmarks\Fixtures\Documents;
use OpenApi\Tests\Benchmarks\Fixtures\FixtureWriter;
use OpenApi\Tests\Benchmarks\Fixtures\Flavour;
use OpenApi\Tests\Benchmarks\Fixtures\Shape;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * A benchmark compares modes only on a fixture every mode reads the same way. These keep the
 * generator honest: a template change that makes one mode's document differ fails here, before it
 * makes a timing meaningless.
 */
final class FixtureEquivalenceTest extends TestCase
{
    /**
     * @return iterable<string, array{Shape, bool, string}>
     */
    public static function shapesProvider(): iterable
    {
        $shapes = [
            'flat' => new Shape(schemas: 3, depth: 0, perController: 2),
            'nested' => new Shape(schemas: 8, depth: 3, unusedRatio: 0.25, noise: 2, perController: 3),
        ];

        foreach ($shapes as $name => $shape) {
            foreach (['3.0.0', '3.1.0'] as $version) {
                yield "{$name}, cleanup, {$version}" => [$shape, true, $version];
                yield "{$name}, no cleanup, {$version}" => [$shape, false, $version];
            }
        }
    }

    #[DataProvider('shapesProvider')]
    public function testEveryModeBuildsTheSameDocument(Shape $shape, bool $cleanup, string $version): void
    {
        $writer = new FixtureWriter();
        $classic = $writer->write($shape, Flavour::Classic);
        $spec = $writer->write($shape, Flavour::Spec);

        $expected = Documents::normalise($spec->build(Mode::SPEC, $cleanup, version: $version)->toArray());

        $this->assertSame($expected, Documents::normalise($classic->build(Mode::CLASSIC, $cleanup, version: $version)->toArray()), 'classic');
        $this->assertSame($expected, Documents::normalise($classic->build(Mode::HYBRID, $cleanup, version: $version)->toArray()), 'hybrid');
    }

    /**
     * Seeded with the controllers, spec reaches only what they reference, so it matches a scan that
     * removed what nothing references.
     */
    #[DataProvider('shapesProvider')]
    public function testASeededSpecBuildMatchesAScanWithCleanup(Shape $shape, bool $cleanup, string $version): void
    {
        $spec = (new FixtureWriter())->write($shape, Flavour::Spec);

        $this->assertSame(
            Documents::normalise($spec->build(Mode::SPEC, cleanup: true, version: $version)->toArray()),
            Documents::normalise($spec->build(Mode::SPEC, $cleanup, scan: false, version: $version)->toArray()),
        );
    }

    public function testTheUnusedSchemasAreTheOnesCleanupRemoves(): void
    {
        $shape = new Shape(schemas: 10, depth: 1, unusedRatio: 0.4);
        $spec = (new FixtureWriter())->write($shape, Flavour::Spec);

        $this->assertCount($shape->used(), $spec->build(Mode::SPEC)->toArray()['components']['schemas']);
        $this->assertCount($shape->schemas, $spec->build(Mode::SPEC, cleanup: false)->toArray()['components']['schemas']);
    }
}
