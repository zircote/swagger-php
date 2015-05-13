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
}
