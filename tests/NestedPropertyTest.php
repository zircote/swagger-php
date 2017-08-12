<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace SwaggerTests;

use Swagger\Processors\MergeIntoComponents;
use Swagger\Processors\AugmentProperties;
use Swagger\Processors\AugmentSchemas;
use Swagger\Processors\MergeIntoOpenApi;
use Swagger\StaticAnalyser;

class NestedPropertyTest extends SwaggerTestCase
{
    public function testNestedProperties()
    {
        $analyser = new StaticAnalyser();
        $analysis = $analyser->fromFile(__DIR__ . '/Fixtures/NestedProperty.php');
        $analysis->process(new MergeIntoOpenApi());
        $analysis->process(new MergeIntoComponents());
        $analysis->process(new AugmentSchemas());
        $analysis->process(new AugmentProperties());

        $this->assertCount(1, $analysis->openapi->components->schemas);
        $schema = $analysis->openapi->components->schemas[0];
        $this->assertEquals('NestedProperty', $schema->schema);
        $this->assertCount(1, $schema->properties);

        $parentProperty = $schema->properties[0];
        $this->assertEquals('parentProperty', $parentProperty->property);
        $this->assertCount(1, $parentProperty->properties);

        $babyProperty = $parentProperty->properties[0];
        $this->assertEquals('babyProperty', $babyProperty->property);
        $this->assertCount(1, $babyProperty->properties);

        $theBabyOfBaby = $babyProperty->properties[0];
        $this->assertEquals('theBabyOfBaby', $theBabyOfBaby->property);
        $this->assertCount(1, $theBabyOfBaby->properties);

        // verbose not-recommend notations
        $theBabyOfBabyBaby = $theBabyOfBaby->properties[0];
        $this->assertEquals('theBabyOfBabyBaby', $theBabyOfBabyBaby->property);
        $this->assertNull($theBabyOfBabyBaby->properties);
    }
}
