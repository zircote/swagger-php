<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Processors;

use OpenApi\Analysis;
use OpenApi\Annotations\Components;
use OpenApi\Annotations\Info;
use OpenApi\Annotations\PathItem;
use OpenApi\Annotations\Schema;
use OpenApi\Generator;
use OpenApi\Processors\AugmentProperties;
use OpenApi\Processors\AugmentSchemas;
use OpenApi\Processors\BuildPaths;
use OpenApi\Processors\CleanUnmerged;
use OpenApi\Processors\ExpandClasses;
use OpenApi\Processors\ExpandInterfaces;
use OpenApi\Processors\ExpandTraits;
use OpenApi\Processors\MergeIntoComponents;
use OpenApi\Processors\MergeIntoOpenApi;
use OpenApi\Tests\OpenApiTestCase;

class ExpandClassesTest extends OpenApiTestCase
{
    protected function validate(Analysis $analysis)
    {
        $analysis->openapi->info = new Info(['title' => 'test', 'version' => '1.0.0', '_context' => $this->getContext()]);
        $analysis->openapi->paths = [new PathItem(['path' => '/test', '_context' => $this->getContext()])];
        $analysis->validate();
    }

    public function testExpandClasses(): void
    {
        $analysis = $this->analysisFromFixtures(
            [
                'AnotherNamespace/Child.php',
                'ExpandClasses/GrandAncestor.php',
                'ExpandClasses/Ancestor.php',
            ]
        );
        $analysis->process([
            new MergeIntoOpenApi(),
            new MergeIntoComponents(),
            new ExpandInterfaces(),
            new ExpandTraits(),
            new AugmentSchemas(),
            new AugmentProperties(),
            new BuildPaths(),
        ]);
        $this->validate($analysis);

        /** @var Schema[] $schemas */
        $schemas = $analysis->getAnnotationsOfType(Schema::class);
        $childSchema = $schemas[0];
        $this->assertSame('Child', $childSchema->schema);
        $this->assertCount(1, $childSchema->properties);

        $analysis->process([
            new ExpandClasses(),
            new CleanUnmerged(),
        ]);
        $this->validate($analysis);

        $this->assertCount(3, $childSchema->properties);
    }

    /**
     * Tests, if ExpandClasses works even without any
     * docBlocks at all in the parent class.
     */
    public function testExpandClassesWithoutDocBlocks(): void
    {
        $analysis = $this->analysisFromFixtures([
            // this class has docblocks
            'AnotherNamespace/ChildWithDocBlocks.php',
            // this one doesn't
            'ExpandClasses/AncestorWithoutDocBlocks.php',
        ]);
        $analysis->process((new Generator())->getProcessors());
        $this->validate($analysis);

        /** @var Schema[] $schemas */
        $schemas = $analysis->getAnnotationsOfType(Schema::class);
        $childSchema = $schemas[0];
        $this->assertSame('ChildWithDocBlocks', $childSchema->schema);
        $this->assertCount(1, $childSchema->properties);

        // no error occurs
        $analysis->process([new ExpandClasses()]);
        $this->assertCount(1, $childSchema->properties);
    }

    /**
     * Tests inherit properties with all of block.
     */
    public function testExpandClassesWithAllOf(): void
    {
        $analysis = $this->analysisFromFixtures([
            // this class has all of
            'ExpandClasses/Extended.php',
            'ExpandClasses/Base.php',
        ]);
        $analysis->process((new Generator())->getProcessors());
        $this->validate($analysis);

        /** @var Schema[] $schemas */
        $schemas = $analysis->getAnnotationsOfType(Schema::class, true);
        $this->assertCount(3, $schemas);

        $extendedSchema = $schemas[0];
        $this->assertSame('ExtendedModel', $extendedSchema->schema);
        $this->assertSame(Generator::UNDEFINED, $extendedSchema->properties);

        $this->assertArrayHasKey(0, $extendedSchema->allOf);
        $this->assertEquals($extendedSchema->allOf[2]->properties[0]->property, 'extendedProperty');

        $includeSchemaWithRef = $schemas[1];
        $this->assertSame(Generator::UNDEFINED, $includeSchemaWithRef->properties);
    }

    /**
     * Tests for inherit properties without all of block.
     */
    public function testExpandClassesWithOutAllOf(): void
    {
        $analysis = $this->analysisFromFixtures([
            // this class has all of
            'ExpandClasses/ExtendedWithoutAllOf.php',
            'ExpandClasses/Base.php',
        ]);
        $analysis->process((new Generator())->getProcessors());
        $this->validate($analysis);

        /** @var Schema[] $schemas */
        $schemas = $analysis->getAnnotationsOfType(Schema::class, true);
        $this->assertCount(2, $schemas);

        $extendedSchema = $schemas[0];
        $this->assertSame('ExtendedWithoutAllOf', $extendedSchema->schema);
        $this->assertSame(Generator::UNDEFINED, $extendedSchema->properties);

        $this->assertCount(2, $extendedSchema->allOf);

        $this->assertEquals(Components::ref('Base'), $extendedSchema->allOf[0]->ref);
        $this->assertEquals('extendedProperty', $extendedSchema->allOf[1]->properties[0]->property);
    }

    /**
     * Tests for inherit properties in object with two schemas in the same context.
     */
    public function testExpandClassesWithTwoChildSchemas(): void
    {
        $analysis = $this->analysisFromFixtures([
            // this class has all of
            'ExpandClasses/ExtendedWithTwoSchemas.php',
            'ExpandClasses/Base.php',
        ]);
        $analysis->process((new Generator())->getProcessors());
        $this->validate($analysis);

        /** @var Schema[] $schemas */
        $schemas = $analysis->getAnnotationsOfType(Schema::class, true);
        $this->assertCount(3, $schemas);

        $extendedSchema = $schemas[0];
        $this->assertSame('ExtendedWithTwoSchemas', $extendedSchema->schema);
        $this->assertSame(Generator::UNDEFINED, $extendedSchema->properties);

        $this->assertCount(2, $extendedSchema->allOf);
        $this->assertEquals(Components::ref('Base'), $extendedSchema->allOf[0]->ref);
        $this->assertEquals('nested', $extendedSchema->allOf[1]->properties[1]->property);
        $this->assertEquals('extendedProperty', $extendedSchema->allOf[1]->properties[0]->property);

        $nestedSchema = $schemas[1];
        $this->assertCount(2, $nestedSchema->allOf);
        $this->assertCount(1, $nestedSchema->allOf[1]->properties);
        $this->assertEquals('nestedProperty', $nestedSchema->allOf[1]->properties[0]->property);
    }

    /**
     * Tests inherit properties with interface.
     */
    public function testPreserveExistingAllOf(): void
    {
        $analysis = $this->analysisFromFixtures([
            'ExpandClasses/BaseInterface.php',
            'ExpandClasses/ExtendsBaseThatImplements.php',
            'ExpandClasses/BaseThatImplements.php',
            'ExpandClasses/TraitUsedByExtendsBaseThatImplements.php',
        ]);
        $analysis->process((new Generator())->getProcessors());
        $this->validate($analysis);

        $analysis->openapi->info = new Info(['title' => 'test', 'version' => '1.0.0', '_context' => $this->getContext()]);
        $analysis->openapi->paths = [new PathItem(['path' => '/test', '_context' => $this->getContext()])];
        $analysis->validate();

        /** @var Schema[] $schemas */
        $schemas = $analysis->getAnnotationsOfType(Schema::class, true);
        $this->assertCount(4, $schemas);

        $baseInterface = $schemas[0];
        $this->assertSame('BaseInterface', $baseInterface->schema);
        $this->assertEquals($baseInterface->properties[0]->property, 'interfaceProperty');
        $this->assertEquals(Generator::UNDEFINED, $baseInterface->allOf);

        $extendsBaseThatImplements = $schemas[1];
        $this->assertSame('ExtendsBaseThatImplements', $extendsBaseThatImplements->schema);
        $this->assertEquals(Generator::UNDEFINED, $extendsBaseThatImplements->properties);
        $this->assertNotEquals(Generator::UNDEFINED, $extendsBaseThatImplements->allOf);
        // base, trait and own properties
        $this->assertCount(3, $extendsBaseThatImplements->allOf);

        $baseThatImplements = $schemas[2];
        $this->assertSame('BaseThatImplements', $baseThatImplements->schema);
        $this->assertEquals(Generator::UNDEFINED, $baseThatImplements->properties);
        $this->assertNotEquals(Generator::UNDEFINED, $baseThatImplements->allOf);
        $this->assertCount(2, $baseThatImplements->allOf);

        $traitUsedByExtendsBaseThatImplements = $schemas[3];
        $this->assertSame('TraitUsedByExtendsBaseThatImplements', $traitUsedByExtendsBaseThatImplements->schema);
        $this->assertEquals($traitUsedByExtendsBaseThatImplements->properties[0]->property, 'traitProperty');
        $this->assertEquals(Generator::UNDEFINED, $traitUsedByExtendsBaseThatImplements->allOf);
    }
}
