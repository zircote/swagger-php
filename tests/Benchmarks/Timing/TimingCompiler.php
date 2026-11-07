<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Benchmarks\Timing;

use OpenApi\Contracts\CompilerInterface;
use OpenApi\Specification;

/**
 * Times the validation and compilation of the compiler it wraps.
 */
class TimingCompiler implements CompilerInterface
{
    public function __construct(protected CompilerInterface $compiler, protected Timings $timings)
    {
    }

    public function getVersion(): string
    {
        return $this->compiler->getVersion();
    }

    public function supports(string $version): bool
    {
        return $this->compiler->supports($version);
    }

    public function compile(Specification $specification): array
    {
        return $this->timings->time('compile', fn (): array => $this->compiler->compile($specification));
    }

    public function validate(Specification $specification): array
    {
        return $this->timings->time('validate', fn (): array => $this->compiler->validate($specification));
    }
}
