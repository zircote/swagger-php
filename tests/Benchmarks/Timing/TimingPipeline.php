<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Benchmarks\Timing;

use OpenApi\Specification;
use OpenApi\Utils\Pipeline;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

/**
 * A pipeline that times each pipe, under `<prefix><short class name>`. A class that runs more
 * than once gets its run number from the second run on: `Merge`, then `Merge 2`.
 *
 * @extends Pipeline<Specification>
 */
class TimingPipeline extends Pipeline
{
    public Timings $timings;

    public string $prefix = '';

    public function process(mixed $payload)
    {
        $timings = $this->timings;
        $runs = [];

        // each pipe timed on its own
        foreach ($this->ordered() as $pipe) {
            if ($pipe instanceof LoggerAwareInterface && $this->logger instanceof LoggerInterface) {
                $pipe->setLogger($this->logger);
            }

            $name = (new \ReflectionClass($pipe))->getShortName();
            $runs[$name] = ($runs[$name] ?? 0) + 1;
            $phase = $this->prefix . $name . ($runs[$name] > 1 ? ' ' . $runs[$name] : '');

            $payload = $timings->time($phase, static fn (): mixed => $pipe($payload)) ?? $payload;
        }

        return $payload;
    }
}
