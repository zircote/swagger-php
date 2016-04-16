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

        $this->assertSame('api/test1', $swagger->paths[0]->path);
        $this->assertSame('Example summary', $swagger->paths[0]->get->summary, 'Operation summary should be taken from phpDoc');
        $this->assertSame('Example description... More description...', $swagger->paths[0]->get->description, 'Operation description should be taken from phpDoc');

        $this->assertSame('api/test2', $swagger->paths[1]->path);
        $this->assertSame('Example summary', $swagger->paths[1]->get->summary, 'Operation summary should be taken from phpDoc');
        $this->assertNull($swagger->paths[1]->get->description, 'Operation description should be taken from phpDoc');
    }
}
