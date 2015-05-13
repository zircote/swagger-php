<?php

/**
 * @license Apache 2.0
 */

namespace SwaggerTests;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Swagger\Scanner;

class ReflectingAnalyserTest extends SwaggerTestCase
{
    public static function setUpBeforeClass()
    {
        $loader = require dirname(__DIR__) . '/vendor/autoload.php';
        AnnotationRegistry::registerLoader([$loader, 'loadClass']);
    }

    public function testTrait()
    {
        $swagger = Scanner::createReflecting()->scan(__DIR__ . '/Fixtures/Shop');
        $this->assertSwaggerEqualsFile(__DIR__ . '/Fixtures/Output/Shop.json', $swagger);
    }

    /**
     * @dataProvider getExamples
     */
    public function testExample($exampleDir, $outputFile)
    {
        $swagger = Scanner::createReflecting()->scan(dirname(__DIR__) . '/Examples/' . $exampleDir);
        $this->assertSwaggerEqualsFile(__DIR__ . '/ExamplesOutput/' . $outputFile, $swagger);
    }

    public function getExamples()
    {
        return [
            ['petstore.swagger.io', 'petstore.swagger.io.json'],
            ['swagger-spec/Petstore', 'petstore.json'],
            ['swagger-spec/PetstoreSimple', 'petstore-simple.json'],
            ['swagger-spec/PetstoreWithExternalDocs', 'petstore-with-external-docs.json'],
        ];
    }
}
