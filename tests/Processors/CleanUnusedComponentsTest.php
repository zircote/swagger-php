<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Processors;

use OpenApi\Generator;
use OpenApi\Processors\CleanUnusedComponents;
use OpenApi\Tests\OpenApiTestCase;

class CleanUnusedComponentsTest extends OpenApiTestCase
{
    public static function countCases(): iterable
    {
        $defaultProcessors = static::processors([CleanUnusedComponents::class]);

        return [
            'var-default' => [$defaultProcessors, 'UsingVar.php', 2, 5],
            'var-clean' => [array_merge($defaultProcessors, [new CleanUnusedComponents(true)]), 'UsingVar.php', 0, 2],
            'unreferenced-default' => [$defaultProcessors, 'Unreferenced.php', 2, 11],
            'unreferenced-clean' => [array_merge($defaultProcessors, [new CleanUnusedComponents(true)]), 'Unreferenced.php', 0, 5],
        ];
    }

    /**
     * @dataProvider countCases
     */
    public function testCounts(array $processors, string $fixture, int $expectedSchemaCount, int $expectedAnnotationCount): void
    {
        $analysis = $this->analysisFromFixtures([$fixture], $processors);

        if ($expectedSchemaCount === 0) {
            $this->assertTrue(Generator::isDefault($analysis->openapi->components->schemas));
        } else {
            $this->assertCount($expectedSchemaCount, $analysis->openapi->components->schemas);
        }
        $this->assertCount($expectedAnnotationCount, $analysis->annotations);
    }
}
