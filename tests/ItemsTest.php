<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace SwaggerTests;

class ItemsTest extends SwaggerTestCase
{
    public function testTypeArray()
    {
        $annotations = $this->parseComment('@OAS\Items(type="array")');
        $this->assertSwaggerLogEntryStartsWith('@OAS\Items() is required when @OAS\Items() has type "array" in ');
        $annotations[0]->validate();
    }

//    public function testTypeObject()
//    {
//        $notAllowedInQuery = $this->parseComment('@OAS\Parameter(name="param",in="query",type="array",@OAS\Items(type="object"))');
//        $this->assertSwaggerLogEntryStartsWith('@OAS\Items()->type="object" not allowed inside a @OAS\Parameter() must be "string", "number", "integer", "boolean", "array" in ');
//        $notAllowedInQuery[0]->validate();
//    }
}
