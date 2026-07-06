<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Processors;

use OpenApi\Tests\OpenApiTestCase;
use OpenApi\Undefined;
use PHPUnit\Framework\Attributes\DataProvider;

final class CleanUnusedComponentsTest extends OpenApiTestCase
{
    public static function countCases(): \Iterator
    {
        $configEnable = ['cleanUnusedComponents' => ['enabled' => true]];
        yield 'var-default' => [[], 'UsingVar.php', 2, 5];
        yield 'var-clean' => [$configEnable, 'UsingVar.php', 0, 1];
        yield 'unreferenced-default' => [[], 'Unreferenced.php', 2, 14];
        yield 'unreferenced-clean' => [$configEnable, 'Unreferenced.php', 0, 5];
    }

    #[DataProvider('countCases')]
    public function testCounts(array $config, string $fixture, int $expectedSchemaCount, int $expectedAnnotationCount): void
    {
        $analysis = $this->analysisFromFixtures(
            [$fixture],
            $this->processorPipeline(),
            config: $config
        );

        if ($expectedSchemaCount === 0) {
            $this->assertTrue(Undefined::isDefault($analysis->openapi->components->schemas));
        } else {
            $this->assertCount($expectedSchemaCount, $analysis->openapi->components->schemas);
        }
        $this->assertCount($expectedAnnotationCount, $analysis->annotations);
    }
}
