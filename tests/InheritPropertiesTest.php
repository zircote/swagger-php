<?php

/**
 * @license Apache 2.0
 */

namespace SwaggerTests;

use Swagger\Annotations\Info;
use Swagger\Processors\AugmentDefinitions;
use Swagger\Processors\AugmentProperties;
use Swagger\Processors\InheritProperties;
use Swagger\Processors\MergeIntoSwagger;
use Swagger\StaticAnalyser;

class InheritPropertiesTest extends SwaggerTestCase
{

    public function testInheritProperties()
    {
        $analyser = new StaticAnalyser();
        $analysis = $analyser->fromFile(__DIR__ . '/Fixtures/Child.php');
        $analysis->addAnalysis($analyser->fromFile(__DIR__ . '/Fixtures/GrandParent.php'));
        $analysis->addAnalysis($analyser->fromFile(__DIR__ . '/Fixtures/Parent.php'));
        $analysis->process([
            new MergeIntoSwagger(),
            new AugmentDefinitions(),
            new AugmentProperties()
        ]);
        $definitions = $analysis->getAnnotationsOfType('\Swagger\Annotations\Definition');
        $childDefinition = $definitions[0];
        $this->assertSame('Child', $childDefinition->definition);
        $this->assertCount(1, $childDefinition->properties);
        $analysis->process(new InheritProperties());
        $this->assertCount(3, $childDefinition->properties);
        
        $analysis->swagger->info = new Info(['title' => 'test', 'version' => 1]);
        $analysis->validate();
    }
}