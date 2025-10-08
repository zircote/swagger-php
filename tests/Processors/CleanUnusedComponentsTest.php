<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Processors;

use OpenApi\Generator;
use OpenApi\Tests\OpenApiTestCase;

class CleanUnusedComponentsTest extends OpenApiTestCase
{
    public static function countCases(): iterable
    {
        $configEnable = ['cleanUnusedComponents' => ['enabled' => true]];

        return [
            'var-default' => [[], 'UsingVar.php', 2, 5],
            'var-clean' => [$configEnable, 'UsingVar.php', 0, 2],
            'unreferenced-default' => [[], 'Unreferenced.php', 2, 11],
            'unreferenced-clean' => [$configEnable, 'Unreferenced.php', 0, 5],
        ];
    }

    /**
     * @dataProvider countCases
     */
    public function testCounts(array $config, string $fixture, int $expectedSchemaCount, int $expectedAnnotationCount): void
    {
        $analysis = $this->analysisFromFixtures(
            [$fixture],
            $this->processorPipeline(),
            config: $config
        );

        if ($expectedSchemaCount === 0) {
            $this->assertTrue(Generator::isDefault($analysis->openapi->components->schemas));
        } else {
            $this->assertCount($expectedSchemaCount, $analysis->openapi->components->schemas);
        }
        $this->assertCount($expectedAnnotationCount, $analysis->annotations);
    }
}
