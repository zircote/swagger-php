<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace SwaggerTests;

use Swagger\Processors\AugmentProperties;
use Swagger\Processors\AugmentSchemas;
use Swagger\Processors\MergeIntoComponents;
use Swagger\Processors\MergeIntoOpenApi;
use Swagger\StaticAnalyser;

class AugmentPropertiesTest extends SwaggerTestCase
{
    public function testAugmentProperties()
    {
        $analyser = new StaticAnalyser();
        $analysis = $analyser->fromFile(__DIR__.'/Fixtures/Customer.php');
        $analysis->process(new MergeIntoOpenApi());
        $analysis->process(new MergeIntoComponents());
        $analysis->process(new AugmentSchemas());
        $customer = $analysis->openapi->components->schemas[0];
        $firstname = $customer->properties[0];
        $lastname = $customer->properties[1];
        $tags = $customer->properties[2];
        $submittedBy = $customer->properties[3];
        $friends = $customer->properties[4];

        // Verify no values where defined in the annotation.
        $this->assertNull($firstname->property);
        $this->assertNull($firstname->description);
        $this->assertNull($firstname->type);

        $this->assertNull($lastname->property);
        $this->assertNull($lastname->description);
        $this->assertNull($lastname->type);

        $this->assertNull($tags->property);
        $this->assertNull($tags->type);
        $this->assertNull($tags->items);

        $this->assertNull($submittedBy->property);
        $this->assertNull($submittedBy->ref);

        $this->assertNull($friends->property);
        $this->assertNull($friends->type);

        $analysis->process(new AugmentProperties());

        $this->assertSame('firstname', $firstname->property, '@OAS\Property()->property based on propertyname');
        $this->assertEquals('test_user', $firstname->example);
        $this->assertSame('The firstname of the customer.', $firstname->description, '@OAS\Property()->description based on @var description');
        $this->assertSame('string', $firstname->type, '@OAS\Property()->type based on @var declaration');

        $this->assertSame('lastname', $lastname->property);
        $this->assertSame('The lastname of the customer.', $lastname->description);
        $this->assertSame('string', $lastname->type);

        $this->assertSame('tags', $tags->property);
        $this->assertSame('array', $tags->type, 'Detect array notation: @var string[]');
        $this->assertSame('string', $tags->items->type);

        $this->assertSame('submittedBy', $submittedBy->property);
        $this->assertSame('#/components/schemas/Customer', $submittedBy->ref);

        $this->assertSame('friends', $friends->property);
        $this->assertSame('array', $friends->type);
        $this->assertSame('#/components/schemas/Customer', $friends->items->ref);
    }
}
