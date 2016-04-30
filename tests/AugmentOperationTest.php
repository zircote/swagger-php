<?php

/**
 * @license Apache 2.0
 */

namespace Swaggertests;

use Swagger\Processors\AugmentOperations;
use Swagger\StaticAnalyser;

class AugmentOperationTest extends SwaggerTestCase
{

    public function testAugmentOperation()
    {
        $analyser = new StaticAnalyser();
        $analysis = $analyser->fromFile(__DIR__.'/Fixtures/UsingPhpDoc.php');
        $analysis->process([
            new AugmentOperations(),
        ]);
        $operations = $analysis->getAnnotationsOfType('\Swagger\Annotations\Operation');

        $this->assertSame('api/test1', $operations[0]->path);
        $this->assertSame('Example summary', $operations[0]->summary, 'Operation summary should be taken from phpDoc');
        $this->assertSame("Example description...\nMore description...", $operations[0]->description, 'Operation description should be taken from phpDoc');

        $this->assertSame('api/test2', $operations[1]->path);
        $this->assertSame('Example summary', $operations[1]->summary, 'Operation summary should be taken from phpDoc');
        $this->assertNull($operations[1]->description, 'This operation only has summary in the phpDoc, no description');
    }
}
