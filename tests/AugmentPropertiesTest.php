<?php

/**
 * @license Apache 2.0
 */

namespace SwaggerTests;

use Swagger\Annotations\Property;
use Swagger\Processors\AugmentDefinitions;
use Swagger\Processors\AugmentProperties;
use Swagger\Processors\MergeIntoSwagger;
use Swagger\StaticAnalyser;

class AugmentPropertiesTest extends SwaggerTestCase
{
    const KEY_PROPERTY = 'property';
    const KEY_DESCRIPTION = 'description';
    const KEY_TYPE = 'type';

    public function testAugmentProperties()
    {
        $analyser = new StaticAnalyser();
        $analysis = $analyser->fromFile(__DIR__ . '/Fixtures/Customer.php');
        $analysis->process(new MergeIntoSwagger());
        $this->assertCount(1, $analysis->swagger->definitions);
        $customer = $analysis->swagger->definitions[0];
        $this->assertNull($customer->properties, 'Sanity check. @SWG\Property\'s not yet merged ');
        $analysis->process(new AugmentDefinitions());
        $analysis->process(new AugmentProperties());

        $firstName = $customer->properties[0];
        $secondName = $customer->properties[1];
        $thirdName = $customer->properties[2];
        $fourthName = $customer->properties[3];
        $lastName = $customer->properties[4];
        $tags = $customer->properties[5];
        $submittedBy = $customer->properties[6];
        $friends = $customer->properties[7];

        $expectedValues = [
            self::KEY_PROPERTY => 'firstname',
            self::KEY_DESCRIPTION => 'The first name of the customer.',
            self::KEY_TYPE => 'string',
        ];
        $this->assertName($firstName, $expectedValues);

        $expectedValues = [
            self::KEY_PROPERTY => 'secondname',
            self::KEY_DESCRIPTION => 'The second name of the customer.',
            self::KEY_TYPE => 'string',
        ];
        $this->assertName($secondName, $expectedValues);

        $expectedValues = [
            self::KEY_PROPERTY => 'thirdname',
            self::KEY_DESCRIPTION => 'The third name of the customer.',
            self::KEY_TYPE => 'string',
        ];
        $this->assertName($thirdName, $expectedValues);

        $expectedValues = [
            self::KEY_PROPERTY => 'fourthname',
            self::KEY_DESCRIPTION => 'The unknown name of the customer.',
            self::KEY_TYPE => null,
        ];
        $this->assertName($fourthName, $expectedValues);

        $expectedValues = [
            self::KEY_PROPERTY => 'lastname',
            self::KEY_DESCRIPTION => 'The lastname of the customer.',
            self::KEY_TYPE => 'string',
        ];
        $this->assertName($lastName, $expectedValues);

        $expectedValues = [
            self::KEY_PROPERTY => 'lastname',
            self::KEY_DESCRIPTION => 'The lastname of the customer.',
            self::KEY_TYPE => 'string',
        ];
        $this->assertName($lastName, $expectedValues);

        $this->assertSame('tags', $tags->property);
        $this->assertSame('array', $tags->type, 'Detect array notation: @var string[]');
        $this->assertSame('string', $tags->items->type);

        $this->assertSame('submittedBy', $submittedBy->property);
        $this->assertSame('#/definitions/Customer', $submittedBy->ref);

        $this->assertSame('friends', $friends->property);
        $this->assertSame('array', $friends->type);
        $this->assertSame('#/definitions/Customer', $friends->items->ref);
    }

    /**
     * @param Property $property
     * @param array $expectedValues
     *
     * @return void
     */
    protected function assertName(Property $property, array $expectedValues)
    {
        foreach ($expectedValues as $key => $val) {
            $this->assertSame($val, $property->$key, sprintf('@SWG\Property()->%s based on propertyname', $key));
        }
    }
}
