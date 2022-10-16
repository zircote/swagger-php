<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Processors;

use OpenApi\Processors\CleanUnusedComponents;
use OpenApi\Tests\OpenApiTestCase;

class CleanUnusedComponentsTest extends OpenApiTestCase
{
    public function countCases(): iterable
    {
        $defaultProcessors = $this->processors([CleanUnusedComponents::class]);

        return [
            'var-default' => [$defaultProcessors, 'UsingVar.php', 2, 5],
            'var-clean' => [array_merge($defaultProcessors, [new CleanUnusedComponents()]), 'UsingVar.php', 0, 2],
            'unreferenced-default' => [$defaultProcessors, 'Unreferenced.php', 2, 11],
            'unreferenced-clean' => [array_merge($defaultProcessors, [new CleanUnusedComponents()]), 'Unreferenced.php', 0, 5],
        ];
    }

    /**
     * @dataProvider countCases
     */
    public function testCounts(array $processors, string $fixture, int $expectedSchemaCount, int $expectedAnnotationCount): void
    {
        $analysis = $this->analysisFromFixtures([$fixture], $processors);

        $this->assertCount($expectedSchemaCount, $analysis->openapi->components->schemas);
        $this->assertCount($expectedAnnotationCount, $analysis->annotations);
    }
}
