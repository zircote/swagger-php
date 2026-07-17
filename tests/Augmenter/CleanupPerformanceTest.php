<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Augmenter;

use OpenApi\Augmenter;
use OpenApi\Builder;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Utils\Pipeline;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Performance regression test for Cleanup augmenter.
 *
 * Run with: phpunit --group performance
 */
#[Group('performance')]
final class CleanupPerformanceTest extends TestCase
{
    protected const SCHEMA_COUNT = 3300;

    protected const UNUSED_RATIO = 0.4;

    protected const MAX_CLEANUP_OVERHEAD_RATIO = 4.0;

    public function testCleanupPerformance(): void
    {
        $spec = $this->buildLargeSpec();

        $withPipeline = (new Builder())->getAugmenters();
        $withoutPipeline = (new Builder())
            /* @phpstan-ignore argument.type */
            ->withAugmenters(fn (Pipeline $pipeline): \OpenApi\Utils\Pipeline => $pipeline->remove(Augmenter\Cleanup::class))
            ->getAugmenters();

        // Warmup
        $withPipeline->process(clone $spec);
        $withoutPipeline->process(clone $spec);

        // Measure without
        $start = microtime(true);
        $withoutPipeline->process(clone $spec);
        $withoutMs = (microtime(true) - $start) * 1000;

        // Measure with
        $start = microtime(true);
        $withPipeline->process(clone $spec);
        $withMs = (microtime(true) - $start) * 1000;

        $ratio = $withoutMs > 0 ? $withMs / $withoutMs : 0;

        fwrite(STDERR, sprintf(
            "\n  Augmenters: %.1fms with Cleanup / %.1fms without = %.2fx overhead (%d schemas, %d%% unused, max: %.1fx)\n",
            $withMs,
            $withoutMs,
            $ratio,
            self::SCHEMA_COUNT,
            self::UNUSED_RATIO * 100,
            self::MAX_CLEANUP_OVERHEAD_RATIO,
        ));

        $this->assertLessThan(
            self::MAX_CLEANUP_OVERHEAD_RATIO,
            $ratio,
            sprintf('Cleanup overhead too high: %.1fx (with: %.1fms, without: %.1fms)', $ratio, $withMs, $withoutMs),
        );
    }

    public function testCleanupRemovesExpectedSchemas(): void
    {
        $spec = $this->buildLargeSpec();
        $usedCount = (int) round(self::SCHEMA_COUNT * (1 - self::UNUSED_RATIO));

        (new Augmenter\Cleanup())($spec);

        $this->assertCount($usedCount, $spec->schemas, 'All unused schemas should be removed');
    }

    protected function buildLargeSpec(): Specification
    {
        $spec = new Specification();

        $usedCount = (int) round(self::SCHEMA_COUNT * (1 - self::UNUSED_RATIO));

        for ($i = 0; $i < self::SCHEMA_COUNT; $i++) {
            $properties = [
                new OA\Property(property: 'id', schema: new OA\Schema(type: 'integer')),
                new OA\Property(property: 'name', schema: new OA\Schema(type: 'string')),
            ];

            if ($i < $usedCount) {
                $refIdx = ($i + 1) % $usedCount;
                $properties[] = new OA\Property(
                    property: 'related',
                    schema: new OA\Schema(ref: '#/components/schemas/PerfSchema' . $refIdx),
                );
            }

            $spec->schemas[] = new OA\Schema(schema: 'PerfSchema' . $i, properties: $properties);
        }

        for ($i = 0; $i < $usedCount; $i++) {
            $operation = new OA\Operation(path: '/perf/' . $i, method: 'get');
            $operation->responses = [
                new OA\Response(response: 200, description: 'OK', content: [
                    new OA\MediaType(
                        mediaType: 'application/json',
                        schema: new OA\Schema(ref: '#/components/schemas/PerfSchema' . $i),
                    ),
                ]),
            ];
            $spec->operations[] = $operation;
        }

        return $spec;
    }
}
