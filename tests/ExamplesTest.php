<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace SwaggerTests;

class ExamplesTest extends SwaggerTestCase
{

    /**
     * Test the processed Examples against json files in ExamplesOutput.
     *
     * @dataProvider getExamples
     * @param string $example Example path
     * @param string $output Expected output (path to a json file)
     */
    public function testExample($example, $output)
    {
        $openapi = \Swagger\scan(__DIR__ . '/../Examples/' . $example);
        //        die((string) $openapi);
        $this->assertSwaggerEqualsFile(__DIR__ . '/ExamplesOutput/' . $output, $openapi);
    }

    /**
     * dataProvider for testExample
     * @return array
     */
    public function getExamples()
    {
        return [
            ['openapi-spec', 'openapi-spec.json'],
            // ['petstore.swagger.io', 'petstore.swagger.io.json'],
            // ['swagger-spec/petstore', 'petstore.json'],
            // ['swagger-spec/petstore-simple', 'petstore-simple.json'],
            // ['swagger-spec/petstore-with-external-docs', 'petstore-with-external-docs.json'],
            // ['using-refs', 'using-refs.json'],
        ];
    }
}
