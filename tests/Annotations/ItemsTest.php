<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Annotations;

use OpenApi\Generator;
use OpenApi\Tests\OpenApiTestCase;

class ItemsTest extends OpenApiTestCase
{
    public function testItemTypeArray()
    {
        $annotations = $this->annotationsFromDocBlockParser('@OA\Items(type="array")');
        $this->assertOpenApiLogEntryContains('@OA\Items() is required when @OA\Items() has type "array" in ');
        $annotations[0]->validate();
    }

    public function testSchemaTypeArray()
    {
        $annotations = $this->annotationsFromDocBlockParser('@OA\Schema(type="array")');
        $this->assertOpenApiLogEntryContains('@OA\Items() is required when @OA\Schema() has type "array" in ');
        $annotations[0]->validate();
    }

    public function testParentTypeArray()
    {
        $annotations = $this->annotationsFromDocBlockParser('@OA\Items() parent type must be "array"');
        $annotations[0]->validate();
    }

    public function testRefDefinitionInProperty()
    {
        $analysis = $this->analysisFromFixtures(['UsingVar.php'], (new Generator())->getProcessors());

        $this->assertCount(2, $analysis->openapi->components->schemas);
        $this->assertEquals('UsingVar', $analysis->openapi->components->schemas[0]->schema);
        $this->assertIsArray($analysis->openapi->components->schemas[0]->properties);
        $this->assertCount(2, $analysis->openapi->components->schemas[0]->properties);
        $this->assertEquals('name', $analysis->openapi->components->schemas[0]->properties[0]->property);
        $this->assertEquals('createdAt', $analysis->openapi->components->schemas[0]->properties[1]->property);
        $this->assertEquals('#/components/schemas/date', $analysis->openapi->components->schemas[0]->properties[1]->ref);
    }
}
