<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApiTests;

use OpenApi\Analysis;
use OpenApi\Logger;
use OpenApi\Processors\InheritInterfaces;
use OpenApi\Processors\InheritTraits;
use OpenApi\Processors\MergeInterfaces;
use OpenApi\Processors\MergeTraits;

class ExamplesTest extends OpenApiTestCase
{

    public function exampleMappings()
    {
        return [
            'misc' => ['misc', 'misc.json', []],
            'openapi-spec' => ['openapi-spec', 'openapi-spec.json', []],
            'petstore.swagger.io' => ['petstore.swagger.io', 'petstore.swagger.io.json', []],
            'petstore-3.0' => ['petstore-3.0', 'petstore-3.0.json', []],
            'swagger-spec/petstore' => ['swagger-spec/petstore', 'petstore.json', []],
            'swagger-spec/petstore-simple' => ['swagger-spec/petstore-simple', 'petstore-simple.json', []],
            'swagger-spec/petstore-with-external-docs' => ['swagger-spec/petstore-with-external-docs', 'petstore-with-external-docs.json', []],
            'using-refs' => ['using-refs', 'using-refs.json', []],
            'example-object' => ['example-object', 'example-object.json', []],
            'using-interfaces-inherit' => ['using-interfaces', 'using-interfaces-inherit.json', []],
            'using-interfaces-merge' => ['using-interfaces', 'using-interfaces-merge.json', $this->processors(InheritInterfaces::class, new MergeInterfaces())],
            'using-traits-inherit' => ['using-traits', 'using-traits-inherit.json', []],
            'using-traits-merge' => ['using-traits', 'using-traits-merge.json', $this->processors(InheritTraits::class, new MergeTraits())],
        ];
    }

    private function processors($fromClass, $to)
    {
        $processors = [];
        foreach (Analysis::processors() as $processor) {
            if ($processor instanceof $fromClass) {
                $processors[] = $to;
            } else {
                $processors[] = $processor;
            }
        }

        return $processors;
    }

    /**
     * Validate openapi definitions of the included examples.
     *
     * @dataProvider exampleMappings
     */
    public function testExamples($example, $output, array $processors)
    {
        Logger::getInstance()->log = function ($entry, $type) {
            // ignore
        };
        $options = [];
        if ($processors) {
            $options['processors'] = $processors;
        }
        $openapi = \OpenApi\scan(__DIR__ . '/../Examples/' . $example, $options);
        $this->assertOpenApiEqualsFile(__DIR__ . '/ExamplesOutput/' . $output, $openapi);
    }
}
