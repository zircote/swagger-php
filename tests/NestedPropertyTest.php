<?php

/**
 * @license Apache 2.0
 */

namespace SwaggerTests;

use Swagger\Processors\AugmentDefinitions;
use Swagger\Processors\AugmentProperties;
use Swagger\Processors\MergeIntoSwagger;
use Swagger\StaticAnalyser;

class NestedPropertyTest extends SwaggerTestCase
{

    public function testNestedProperties()
    {
        $analyser = new StaticAnalyser();
        $analysis = $analyser->fromFile(__DIR__ . '/Fixtures/NestedProperty.php');
        $analysis->process(new MergeIntoSwagger());
        $analysis->process(new AugmentDefinitions());
        $analysis->process(new AugmentProperties());
        
        $this->assertCount(1, $analysis->swagger->definitions);
        $definition = $analysis->swagger->definitions[0];
        $this->assertEquals('NestedProperty', $definition->definition);
        $this->assertCount(1, $definition->properties);

        $parentProperty = $definition->properties[0];
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
