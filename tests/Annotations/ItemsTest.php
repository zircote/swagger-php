<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Annotations;

use OpenApi\Analysers\TokenAnalyser;
use OpenApi\Generator;
use OpenApi\Tests\OpenApiTestCase;

class ItemsTest extends OpenApiTestCase
{
    public function testItemTypeArray()
    {
        $annotations = $this->parseComment('@OA\Items(type="array")');
        $this->assertOpenApiLogEntryContains('@OA\Items() is required when @OA\Items() has type "array" in ');
        $annotations[0]->validate();
    }

    public function testSchemaTypeArray()
    {
        $annotations = $this->parseComment('@OA\Schema(type="array")');
        $this->assertOpenApiLogEntryContains('@OA\Items() is required when @OA\Schema() has type "array" in ');
        $annotations[0]->validate();
    }

    public function testParentTypeArray()
    {
        $annotations = $this->parseComment('@OA\Items() parent type must be "array"');
        $annotations[0]->validate();
    }

    public function testRefDefinitionInProperty()
    {
        $analyser = new TokenAnalyser();
        $analysis = $analyser->fromFile($this->fixture('UsingVar.php'), $this->getContext());
        $analysis->process((new Generator())->getProcessors());

        $this->assertCount(2, $analysis->openapi->components->schemas);
        $this->assertEquals('UsingVar', $analysis->openapi->components->schemas[0]->schema);
        $this->assertIsArray($analysis->openapi->components->schemas[0]->properties);
        $this->assertCount(2, $analysis->openapi->components->schemas[0]->properties);
        $this->assertEquals('name', $analysis->openapi->components->schemas[0]->properties[0]->property);
        $this->assertEquals('createdAt', $analysis->openapi->components->schemas[0]->properties[1]->property);
        $this->assertEquals('#/components/schemas/date', $analysis->openapi->components->schemas[0]->properties[1]->ref);
    }
}
