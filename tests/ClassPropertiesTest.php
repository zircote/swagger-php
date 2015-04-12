<?php

/**
 * @license Apache 2.0
 */

namespace SwaggerTests;

class ClassPropertiesTest extends SwaggerTestCase {

    function testClassPropertiesProcessor() {
        $swagger = \Swagger\scan(__DIR__.'/Fixtures/Customer.php');
        $this->assertCount(1, $swagger->definitions);
        $customer = $swagger->definitions[0];
        $this->assertSame('Customer', $customer->name, '@SWG\Definition()->name based on classname');
        $this->assertCount(5, $customer->properties, '@SWG\Properties() are merged into the @SWG\Definition of the class');
        $firstname = $customer->properties[0];
        $this->assertSame('firstname', $firstname->name, '@SWG\Property()->name based on propertyname');
        $this->assertSame('The firstname of the customer.', $firstname->description, '@SWG\Property()->description based on @var description');
        $this->assertSame('string', $firstname->type, '@SWG\Property()->type based on @var declaration');
        $lastname = $customer->properties[1];
        $this->assertSame('lastname', $lastname->name);
        $this->assertSame('The lastname of the customer.', $lastname->description);
        $this->assertSame('string', $lastname->type);
        $tags = $customer->properties[2];
        $this->assertSame('tags', $tags->name);
        $this->assertSame('array', $tags->type, 'Detect array notation: @var string[]');
        $this->assertSame('string', $tags->items->type);
        $submittedBy = $customer->properties[3];
        $this->assertSame('submittedBy', $submittedBy->name);
        $this->assertSame('#/definitions/Customer', $submittedBy->ref);
        $friends = $customer->properties[4];
        $this->assertSame('friends', $friends->name);
        $this->assertSame('array', $friends->type);
        $this->assertSame('#/definitions/Customer', $friends->items->ref);
    }

}
