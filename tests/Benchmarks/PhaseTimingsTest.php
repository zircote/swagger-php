<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Benchmarks;

use OpenApi\Builder;
use OpenApi\Builder\Mode;
use OpenApi\Tests\Benchmarks\Fixtures\Fixture;
use OpenApi\Tests\Benchmarks\Fixtures\FixtureWriter;
use OpenApi\Tests\Benchmarks\Fixtures\Flavour;
use OpenApi\Tests\Benchmarks\Fixtures\Shape;
use OpenApi\Tests\Benchmarks\Timing\TimingBuilder;
use PHPUnit\Framework\TestCase;

/**
 * Keeps `TimingBuilder` in step with the builder it times: a phase that stops being reached, or an
 * augmenter added without being timed, shows up here rather than as a quietly wrong breakdown.
 */
final class PhaseTimingsTest extends TestCase
{
    /**
     * The phases, and each augmenter in the order a plain `Builder` runs them.
     */
    public function testEveryPhaseOfAHybridBuildIsTimed(): void
    {
        $builder = new TimingBuilder();
        $this->fixture()->build(Mode::HYBRID, builder: $builder);

        $augmenters = [];
        $runs = [];
        (new Builder())->getAugmenters()->walk(static function (object $augmenter) use (&$augmenters, &$runs): void {
            $name = (new \ReflectionClass($augmenter))->getShortName();
            $runs[$name] = ($runs[$name] ?? 0) + 1;
            $augmenters[] = 'augment: ' . $name . ($runs[$name] > 1 ? ' ' . $runs[$name] : '');
        });

        $timings = $builder->timings();
        $this->assertSame(
            ['collect', 'resolve', 'hybrid', ...$augmenters, 'validate', 'compile', 'other', 'total'],
            array_keys($timings),
        );
        $this->assertEqualsWithDelta($timings['total'], array_sum($timings) - $timings['total'], 0.001);
    }

    public function testATimedBuildBuildsTheSameDocument(): void
    {
        $fixture = $this->fixture();

        $this->assertSame(
            $fixture->build(Mode::HYBRID)->toArray(),
            $fixture->build(Mode::HYBRID, builder: new TimingBuilder())->toArray(),
        );
    }

    private function fixture(): Fixture
    {
        return (new FixtureWriter())->write(new Shape(schemas: 3, depth: 0, perController: 2), Flavour::Classic);
    }
}
