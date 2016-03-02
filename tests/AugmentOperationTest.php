<?php

/**
 * @license Apache 2.0
 */

namespace SwaggerTests;

class AugmentOperationTest extends SwaggerTestCase
{
    public function testAugmentOperation()
    {
        $swagger = \Swagger\scan(__DIR__ . '/Fixtures/UsingPhpDoc.php');
        $this->assertSame('api/test', $swagger->paths[0]->path);
        $this->assertEquals('Get protected item', $swagger->paths[0]->get->summary, 'Operation summary should be taken from phpDoc');
        $this->assertEquals('Example description... More description...', $swagger->paths[0]->get->description, 'Operation description should be taken from phpDoc');
    }
}
