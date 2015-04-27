<?php

/**
 * @license Apache 2.0
 */

namespace SwaggerTests;

use Swagger\Processors\InheritProperties;
use Swagger\Processors\ClassProperties;

class InheritPropertiesTest extends SwaggerTestCase
{

    public function testGetChildren()
    {
        $processor = new InheritProperties();
        $swagger = $this->createSwaggerWithInfo();
        $swagger->crawl([
            __DIR__ . '/Fixtures/Parent.php',
            __DIR__ . '/Fixtures/Child.php',
            __DIR__ . '/Fixtures/GrandParent.php',
        ]);

        $children = $processor->getChildren($swagger);
        $this->assertCount(2, $children, '2 classes have children');
        $this->assertSame($children['\\SwaggerFixtures\\Parent'], ['\\AnotherNamespace\\Child']);
        $this->assertSame($children['\\SwaggerFixtures\\GrandParent'], ['\\SwaggerFixtures\\Parent', '\\AnotherNamespace\\Child']);
    }

    public function testInheritWithoutClassProperties()
    {
        $swagger = $this->createSwaggerWithInfo();
        $swagger->crawl([
            __DIR__ . '/Fixtures/Child.php',
            __DIR__ . '/Fixtures/GrandParent.php',
            __DIR__ . '/Fixtures/Parent.php',
        ]);
        $childDefinition = $swagger->definitions[0];
        $this->assertSame('Child', $childDefinition->_context->class);
        $this->assertNull($childDefinition->properties);
        $processor = new InheritProperties();
        $processor($swagger);
        $parentDefinition = $swagger->definitions[1];
        $this->assertSame('Parent', $parentDefinition->_context->class);
        $this->assertCount(1, $parentDefinition->properties); // Inherited 1 property, but is missing its own firstname property
        $this->assertCount(2, $swagger->_unmerged); // only the property in GrandParent was merged
        $this->assertCount(1, $childDefinition->properties); // Inherited 1 property, but couldn't inherit the property from Parent because it didn't have a name, also missing its own isBaby property.
    }

    public function detestInheritProcessor()
    {
        $swagger = $this->createSwaggerWithInfo();
        $swagger->crawl([
            __DIR__ . '/Fixtures/GrandParent.php',
            __DIR__ . '/Fixtures/Child.php',
            __DIR__ . '/Fixtures/Parent.php',
        ]);
        $classPropertiesProcessor = new ClassProperties();
        $classPropertiesProcessor($swagger);
        $this->assertCount(1, $swagger->_unmerged); // only the property in GrandParent is not merged
        $this->assertSame('lastname', $swagger->_unmerged[0]->property);
        $processor = new InheritProperties();
        $processor($swagger);
        $this->assertCount(0, $swagger->_unmerged);
        $childDefinition = $swagger->definitions[0];
        $this->assertSame('Child', $childDefinition->property);
        $this->assertCount(3, $childDefinition->properties);
        $parentDefinition = $swagger->definitions[0];
        $this->assertSame('Parent', $parentDefinition->property);
        $this->assertCount(2, $parentDefinition->properties);
        $swagger->validate();
    }
}
