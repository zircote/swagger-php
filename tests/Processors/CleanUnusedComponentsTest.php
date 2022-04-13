<?php declare(strict_types=1);

namespace OpenApi\Tests\Processors;

use OpenApi\Processors\CleanUnusedComponents;
use OpenApi\Tests\OpenApiTestCase;

class CleanUnusedComponentsTest extends OpenApiTestCase
{
    public function processorCases()
    {
        $defaultProcessors = $this->processors([CleanUnusedComponents::class]);

        return [
            'default' => [$defaultProcessors, 2],
            'clean' => [array_merge($defaultProcessors, [new CleanUnusedComponents()]), 0],
        ];
    }

    /**
     * @dataProvider processorCases
     */
    public function testRefDefinitionInProperty(array $processors, $expectedCount): void
    {
        $analysis = $this->analysisFromFixtures(['UsingVar.php'], $processors);

        $this->assertCount($expectedCount, $analysis->openapi->components->schemas);
    }
}
