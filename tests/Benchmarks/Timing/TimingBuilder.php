<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Benchmarks\Timing;

use OpenApi\Augmenter;
use OpenApi\Builder;
use OpenApi\Builder\Result;
use OpenApi\Contracts\CompilerInterface;
use OpenApi\Specification;

/**
 * A builder that times the phases of a spec or hybrid build, from the outside.
 *
 * `collect` runs from the start of the build to the specification hooks, so it holds the scan,
 * the parsing and the collecting. `other` is what no phase claims. A classic build is timed as
 * `total` only, and `validate` and `compile` are timed only when no compiler is set.
 */
class TimingBuilder extends Builder
{
    protected Timings $timings;

    private int $start = 0;

    public function __construct()
    {
        parent::__construct();

        $this->timings = new Timings();
        $this->resolver = new TimingResolver($this->timings);

        // the default augmenters, in a pipeline that times each one
        $augmenters = new TimingPipeline(
            $this->getDefaultAugmenters(),
            groups: [Augmenter\Group::Resolve, Augmenter\Group::Reduce, Augmenter\Group::Augment],
            defaultGroup: Augmenter\Group::Augment,
            logger: $this->getLogger(),
        );
        $augmenters->timings = $this->timings;
        $augmenters->prefix = 'augment: ';
        $this->augmenters = $augmenters;

        $this->withSpecification(fn () => $this->timings->add('collect', (hrtime(true) - $this->start) / 1e6));
    }

    public function build(): Result
    {
        $this->timings->reset();
        $this->start = hrtime(true);

        return $this->timings->time('total', fn (): Result => parent::build());
    }

    /**
     * The phases of the last build in the order they ran, then `other` and `total`.
     *
     * @return array<string, float>
     */
    public function timings(): array
    {
        $phases = $this->timings->all();
        $total = $phases['total'] ?? 0.0;
        unset($phases['total']);

        return [...$phases, 'other' => max(0.0, $total - array_sum($phases)), 'total' => $total];
    }

    protected function doHybridAssemble(Specification $specification): void
    {
        $this->timings->time('hybrid', fn () => parent::doHybridAssemble($specification));
    }

    protected function resolveCompiler(string $version): CompilerInterface
    {
        return new TimingCompiler(parent::resolveCompiler($version), $this->timings);
    }
}
