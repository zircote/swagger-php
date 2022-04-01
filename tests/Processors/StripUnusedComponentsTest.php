<?php declare(strict_types=1);

namespace OpenApi\Tests\Processors;

use OpenApi\Generator;
use OpenApi\Processors\StripUnusedComponents;
use OpenApi\Tests\OpenApiTestCase;

class StripUnusedComponentsTest extends OpenApiTestCase
{
    public function processorCases()
    {
        $defaultProcessors = (new Generator())->getProcessors();

        return [
            'default' => [$defaultProcessors, 2],
            'stripped' => [array_merge($defaultProcessors, [new StripUnusedComponents()]), 0],
        ];
    }

    /**
     * @dataProvider processorCases
     */
    public function testRefDefinitionInProperty(array $processors, $expectedCount): void
    {
        $analysis = $this->analysisFromFixtures(['UsingVar.php'], $processors);

        echo $analysis->openapi->toYaml();
        $this->assertCount($expectedCount, $analysis->openapi->components->schemas);
    }
}
