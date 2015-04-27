<?php

/**
 * @license Apache 2.0
 */

namespace SwaggerTests;

use Swagger\Annotations\Swagger;
use Swagger\Processors\ClassProperties;

class ClassPropertiesTest extends SwaggerTestCase
{

    public function testClassPropertiesProcessor()
    {
        $processor = new ClassProperties();
        $swagger = new Swagger([]);
        $swagger->crawl(__DIR__ . '/Fixtures/Customer.php');
        $this->assertCount(1, $swagger->definitions);
        $customer = $swagger->definitions[0];
        $this->assertSame(null, $customer->properties, 'Sanity check. @SWG\Property\'s not yet erged ');
        $processor($swagger);
        $this->assertSame('Customer', $customer->definition, '@SWG\Definition()->definition based on classname');
        $this->assertCount(5, $customer->properties, '@SWG\Property()s are merged into the @SWG\Definition of the class');
        $firstname = $customer->properties[0];
        $this->assertSame('firstname', $firstname->property, '@SWG\Property()->property based on propertyname');
        $this->assertSame('The firstname of the customer.', $firstname->description, '@SWG\Property()->description based on @var description');
        $this->assertSame('string', $firstname->type, '@SWG\Property()->type based on @var declaration');
        $lastname = $customer->properties[1];
        $this->assertSame('lastname', $lastname->property);
        $this->assertSame('The lastname of the customer.', $lastname->description);
        $this->assertSame('string', $lastname->type);
        $tags = $customer->properties[2];
        $this->assertSame('tags', $tags->property);
        $this->assertSame('array', $tags->type, 'Detect array notation: @var string[]');
        $this->assertSame('string', $tags->items->type);
        $submittedBy = $customer->properties[3];
        $this->assertSame('submittedBy', $submittedBy->property);
        $this->assertSame('#/definitions/Customer', $submittedBy->ref);
        $friends = $customer->properties[4];
        $this->assertSame('friends', $friends->property);
        $this->assertSame('array', $friends->type);
        $this->assertSame('#/definitions/Customer', $friends->items->ref);
    }
}
