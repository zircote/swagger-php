<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace SwaggerTests;

use Swagger\Annotations\Info;
use Swagger\Annotations\Schema;
use Swagger\Processors\AugmentSchemas;
use Swagger\Processors\AugmentProperties;
use Swagger\Processors\InheritProperties;
use Swagger\Processors\MergeIntoOpenApi;
use Swagger\StaticAnalyser;

class InheritPropertiesTest extends SwaggerTestCase
{
    public function testInheritProperties()
    {
        $analyser = new StaticAnalyser();
        $analysis = $analyser->fromFile(__DIR__ . '/Fixtures/Child.php');
        $analysis->addAnalysis($analyser->fromFile(__DIR__ . '/Fixtures/GrandAncestor.php'));
        $analysis->addAnalysis($analyser->fromFile(__DIR__ . '/Fixtures/Ancestor.php'));
        $analysis->process([
            new MergeIntoOpenApi(),
            new AugmentSchemas(),
            new AugmentProperties()
        ]);
        $schemas = $analysis->getAnnotationsOfType(Schema::class);
        $childSchema = $schemas[0];
        $this->assertSame('Child', $childSchema->schema);
        $this->assertCount(1, $childSchema->properties);
        $analysis->process(new InheritProperties());
        $this->assertCount(3, $childSchema->properties);

        $analysis->openapi->info = new Info(['title' => 'test', 'version' => 1]);
        $analysis->validate();
    }

    /**
     * Tests, if InheritProperties works even without any
     * docBlocks at all in the parent class.
     */
    public function testInheritPropertiesWithoutDocBlocks()
    {
        $analyser = new StaticAnalyser();

        // this class has docblocks
        $analysis = $analyser->fromFile(__DIR__ . '/Fixtures/ChildWithDocBlocks.php');
        // this one doesn't
        $analysis->addAnalysis($analyser->fromFile(__DIR__ . '/Fixtures/AncestorWithoutDocBlocks.php'));

        $analysis->process([
            new MergeIntoOpenApi(),
            new AugmentSchemas(),
            new AugmentProperties()
        ]);
        $schemas = $analysis->getAnnotationsOfType(Schema::class);
        $childSchema = $schemas[0];
        $this->assertSame('ChildWithDocBlocks', $childSchema->schema);
        $this->assertCount(1, $childSchema->properties);

        // no error occurs
        $analysis->process(new InheritProperties());
        $this->assertCount(1, $childSchema->properties);

        $analysis->openapi->info = new Info(['title' => 'test', 'version' => 1]);
        $analysis->validate();
    }
}
