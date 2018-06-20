<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace SwaggerTests;

use Swagger\StaticAnalyser;

class ItemsTest extends SwaggerTestCase
{
    public function testItemTypeArray()
    {
        $annotations = $this->parseComment('@OAS\Items(type="array")');
        $this->assertSwaggerLogEntryStartsWith('@OAS\Items() is required when @OAS\Items() has type "array" in ');
        $annotations[0]->validate();
    }

    public function testSchemaTypeArray()
    {
        $annotations = $this->parseComment('@OAS\Schema(type="array")');
        $this->assertSwaggerLogEntryStartsWith('@OAS\Items() is required when @OAS\Schema() has type "array" in ');
        $annotations[0]->validate();
    }

    public function testTypeObject()
    {
        $this->countExceptions = 1;
        $notAllowedInQuery = $this->parseComment('@OAS\Parameter(name="param",in="query",@OAS\Schema(type="array",@OAS\Items(type="object")))');
        $this->assertSwaggerLogEntryStartsWith('@OAS\Items()->type="object" not allowed inside a @OAS\Parameter() must be "string", "number", "integer", "boolean", "array" in ');
        $notAllowedInQuery[0]->validate();
    }

    public function testRefDefinitionInProperty()
    {
        $analyser = new StaticAnalyser();
        $analysis = $analyser->fromFile(__DIR__.'/Fixtures/UsingVar.php');
        $analysis->process();
        $this->assertCount(1, $analysis->openapi->components->schemas);
        $this->assertEquals('UsingVar', $analysis->openapi->components->schemas[0]->schema);
        $this->assertInternalType('array', $analysis->openapi->components->schemas[0]->properties);
        $this->assertCount(2, $analysis->openapi->components->schemas[0]->properties);
        $this->assertEquals('name', $analysis->openapi->components->schemas[0]->properties[0]->property);
        $this->assertEquals('createdAt', $analysis->openapi->components->schemas[0]->properties[1]->property);
        $this->assertEquals('#/components/schemas/date', $analysis->openapi->components->schemas[0]->properties[1]->ref);
    }
}
