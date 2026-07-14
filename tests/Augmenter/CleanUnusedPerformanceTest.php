<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Augmenter;

use OpenApi\Augmenter;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Performance regression test for CleanUnused augmenter.
 *
 * Run with: phpunit --group performance
 */
#[Group('performance')]
final class CleanUnusedPerformanceTest extends TestCase
{
    protected const SCHEMA_COUNT = 300;

    protected const UNUSED_RATIO = 0.4;

    protected const MAX_CLEANUP_OVERHEAD_MS = 50;

    public function testCleanupPerformance(): void
    {
        $spec = $this->buildLargeSpec();

        // Warmup
        $warmup = clone $spec;
        (new Augmenter\CleanUnused())($warmup);

        // Measure
        $start = microtime(true);
        (new Augmenter\CleanUnused())($spec);
        $elapsed = (microtime(true) - $start) * 1000;

        $this->assertLessThan(
            self::MAX_CLEANUP_OVERHEAD_MS,
            $elapsed,
            sprintf('CleanUnused took %.1fms, max allowed: %dms', $elapsed, self::MAX_CLEANUP_OVERHEAD_MS),
        );
    }

    public function testCleanupRemovesExpectedSchemas(): void
    {
        $spec = $this->buildLargeSpec();
        $usedCount = (int) round(self::SCHEMA_COUNT * (1 - self::UNUSED_RATIO));

        (new Augmenter\CleanUnused())($spec);

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
