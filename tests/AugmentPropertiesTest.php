<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApiTests;

use OpenApi\Processors\AugmentProperties;
use OpenApi\Processors\AugmentSchemas;
use OpenApi\Processors\MergeIntoComponents;
use OpenApi\Processors\MergeIntoOpenApi;
use OpenApi\StaticAnalyser;
use const OpenApi\UNDEFINED;

class AugmentPropertiesTest extends OpenApiTestCase
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
        $this->assertSame(UNDEFINED, $firstname->property);
        $this->assertSame(UNDEFINED, $firstname->description);
        $this->assertSame(UNDEFINED, $firstname->type);

        $this->assertSame(UNDEFINED, $lastname->property);
        $this->assertSame(UNDEFINED, $lastname->description);
        $this->assertSame(UNDEFINED, $lastname->type);

        $this->assertSame(UNDEFINED, $tags->property);
        $this->assertSame(UNDEFINED, $tags->type);
        $this->assertSame(UNDEFINED, $tags->items);

        $this->assertSame(UNDEFINED, $submittedBy->property);
        $this->assertSame(UNDEFINED, $submittedBy->ref);

        $this->assertSame(UNDEFINED, $friends->property);
        $this->assertSame(UNDEFINED, $friends->type);

        $analysis->process(new AugmentProperties());

        $this->assertSame('firstname', $firstname->property, '@OA\Property()->property based on propertyname');
        $this->assertEquals('test_user', $firstname->example);
        $this->assertSame('The firstname of the customer.', $firstname->description, '@OA\Property()->description based on @var description');
        $this->assertSame('string', $firstname->type, '@OA\Property()->type based on @var declaration');

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
