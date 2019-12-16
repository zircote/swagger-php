<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApiTests;

use OpenApi\Annotations\Property;
use OpenApi\Processors\AugmentProperties;
use OpenApi\Processors\AugmentSchemas;
use OpenApi\Processors\MergeIntoComponents;
use OpenApi\Processors\MergeIntoOpenApi;
use OpenApi\StaticAnalyser;
use const OpenApi\UNDEFINED;

/**
 * @group Properties
 */
class AugmentPropertiesTest extends OpenApiTestCase
{
    const KEY_PROPERTY = 'property';
    const KEY_EXAMPLE = 'example';
    const KEY_DESCRIPTION = 'description';
    const KEY_TYPE = 'type';

    public function testAugmentProperties()
    {
        $analyser = new StaticAnalyser();
        $analysis = $analyser->fromFile(__DIR__ . '/Fixtures/Customer.php');
        $analysis->process(new MergeIntoOpenApi());
        $analysis->process(new MergeIntoComponents());
        $analysis->process(new AugmentSchemas());
        $customer = $analysis->openapi->components->schemas[0];
        $firstName = $customer->properties[0];
        $secondName = $customer->properties[1];
        $thirdName = $customer->properties[2];
        $fourthName = $customer->properties[3];
        $lastName = $customer->properties[4];
        $tags = $customer->properties[5];
        $submittedBy = $customer->properties[6];
        $friends = $customer->properties[7];
        $bestFriend = $customer->properties[8];

        // Verify no values where defined in the annotation.
        $this->assertSame(UNDEFINED, $firstName->property);
        $this->assertSame(UNDEFINED, $firstName->description);
        $this->assertSame(UNDEFINED, $firstName->type);

        $this->assertSame(UNDEFINED, $lastName->property);
        $this->assertSame(UNDEFINED, $lastName->description);
        $this->assertSame(UNDEFINED, $lastName->type);

        $this->assertSame(UNDEFINED, $tags->property);
        $this->assertSame(UNDEFINED, $tags->type);
        $this->assertSame(UNDEFINED, $tags->items);

        $this->assertSame(UNDEFINED, $submittedBy->property);
        $this->assertSame(UNDEFINED, $submittedBy->ref);

        $this->assertSame(UNDEFINED, $friends->property);
        $this->assertSame(UNDEFINED, $friends->type);

        $this->assertSame(UNDEFINED, $bestFriend->property);
        $this->assertSame(UNDEFINED, $bestFriend->nullable);
        $this->assertSame(UNDEFINED, $bestFriend->allOf);

        $analysis->process(new AugmentProperties());

        $expectedValues = [
            self::KEY_PROPERTY => 'firstname',
            self::KEY_EXAMPLE => 'John',
            self::KEY_DESCRIPTION => 'The first name of the customer.',
            self::KEY_TYPE => 'string',
        ];
        $this->assertName($firstName, $expectedValues);

        $expectedValues = [
            self::KEY_PROPERTY => 'secondname',
            self::KEY_EXAMPLE => 'Allan',
            self::KEY_DESCRIPTION => 'The second name of the customer.',
            self::KEY_TYPE => 'string',
        ];
        $this->assertName($secondName, $expectedValues);

        $expectedValues = [
            self::KEY_PROPERTY => 'thirdname',
            self::KEY_EXAMPLE => 'Peter',
            self::KEY_DESCRIPTION => 'The third name of the customer.',
            'type' => 'string',
        ];
        $this->assertName($thirdName, $expectedValues);

        $expectedValues = [
            self::KEY_PROPERTY => 'fourthname',
            self::KEY_EXAMPLE => 'Unknown',
            self::KEY_DESCRIPTION => 'The unknown name of the customer.',
            self::KEY_TYPE => '@OA\UNDEFINED🙈',
        ];
        $this->assertName($fourthName, $expectedValues);

        $expectedValues = [
            self::KEY_PROPERTY => 'lastname',
            self::KEY_EXAMPLE => '@OA\UNDEFINED🙈',
            self::KEY_DESCRIPTION => 'The lastname of the customer.',
            self::KEY_TYPE => 'string',
        ];
        $this->assertName($lastName, $expectedValues);

        $this->assertSame('tags', $tags->property);
        $this->assertSame('array', $tags->type, 'Detect array notation: @var string[]');
        $this->assertSame('string', $tags->items->type);

        $this->assertSame('submittedBy', $submittedBy->property);
        $this->assertSame('#/components/schemas/Customer', $submittedBy->ref);

        $this->assertSame('friends', $friends->property);
        $this->assertSame('array', $friends->type);
        $this->assertSame('#/components/schemas/Customer', $friends->items->ref);

        $this->assertSame('bestFriend', $bestFriend->property);
        $this->assertTrue($bestFriend->nullable);
        $this->assertSame('#/components/schemas/Customer', $bestFriend->oneOf[0]->ref);
    }

    public function testTypedProperties()
    {
        $analyser = new StaticAnalyser();
        $analysis = $analyser->fromFile(__DIR__ . '/Fixtures/TypedProperties.php');
        $analysis->process(new MergeIntoOpenApi());
        $analysis->process(new MergeIntoComponents());
        $analysis->process(new AugmentSchemas());
        [
            $stringType,
            $intType,
            $nullableString,
            $dateTime,
            $nativeTrumpsVar,
            $annotationTrumpsNative,
            $annotationTrumpsAll,
            $undefined,
            $onlyAnnotated,
            $staticUndefined,
            $staticString,
            $staticNullableString,
        ] = $analysis->openapi->components->schemas[0]->properties;

        $this->assertName($stringType, [
            self::KEY_PROPERTY => UNDEFINED,
            self::KEY_TYPE => UNDEFINED,
        ]);
        $this->assertName($intType, [
            self::KEY_PROPERTY => UNDEFINED,
            self::KEY_TYPE => UNDEFINED,
        ]);
        $this->assertName($nullableString, [
            self::KEY_PROPERTY => UNDEFINED,
            self::KEY_TYPE => UNDEFINED,
        ]);
        $this->assertName($dateTime, [
            self::KEY_PROPERTY => UNDEFINED,
            self::KEY_TYPE => UNDEFINED,
        ]);
        $this->assertName($nativeTrumpsVar, [
            self::KEY_PROPERTY => UNDEFINED,
            self::KEY_TYPE => UNDEFINED,
        ]);
        $this->assertName($annotationTrumpsNative, [
            self::KEY_PROPERTY => UNDEFINED,
            self::KEY_TYPE => 'int',
        ]);
        $this->assertName($annotationTrumpsAll, [
            self::KEY_PROPERTY => UNDEFINED,
            self::KEY_TYPE => 'int',
        ]);
        $this->assertName($undefined, [
            self::KEY_PROPERTY => UNDEFINED,
            self::KEY_TYPE => UNDEFINED,
        ]);
        $this->assertName($onlyAnnotated, [
            self::KEY_PROPERTY => UNDEFINED,
            self::KEY_TYPE => 'int',
        ]);
        $this->assertName($staticUndefined, [
            self::KEY_PROPERTY => UNDEFINED,
            self::KEY_TYPE => UNDEFINED,
        ]);
        $this->assertName($staticString, [
            self::KEY_PROPERTY => UNDEFINED,
            self::KEY_TYPE => UNDEFINED,
        ]);
        $this->assertName($staticNullableString, [
            self::KEY_PROPERTY => UNDEFINED,
            self::KEY_TYPE => UNDEFINED,
        ]);

        $analysis->process(new AugmentProperties());

        $this->assertName($stringType, [
            self::KEY_PROPERTY => 'stringType',
            self::KEY_TYPE => 'string',
        ]);
        $this->assertName($intType, [
            self::KEY_PROPERTY => 'intType',
            self::KEY_TYPE => 'int',
        ]);
        $this->assertName($nullableString, [
            self::KEY_PROPERTY => 'nullableString',
            self::KEY_TYPE => 'string',
        ]);
        $this->assertName($dateTime, [
            self::KEY_PROPERTY => 'dateTime',
            self::KEY_TYPE => 'DateTime',
        ]);
        $this->assertName($nativeTrumpsVar, [
            self::KEY_PROPERTY => 'nativeTrumpsVar',
            self::KEY_TYPE => 'string',
        ]);
        $this->assertName($annotationTrumpsNative, [
            self::KEY_PROPERTY => 'annotationTrumpsNative',
            self::KEY_TYPE => 'int',
        ]);
        $this->assertName($annotationTrumpsAll, [
            self::KEY_PROPERTY => 'annotationTrumpsAll',
            self::KEY_TYPE => 'int',
        ]);
        $this->assertName($undefined, [
            self::KEY_PROPERTY => 'undefined',
            self::KEY_TYPE => UNDEFINED,
        ]);
        $this->assertName($onlyAnnotated, [
            self::KEY_PROPERTY => 'onlyAnnotated',
            self::KEY_TYPE => 'int',
        ]);
        $this->assertName($staticUndefined, [
            self::KEY_PROPERTY => 'staticUndefined',
            self::KEY_TYPE => UNDEFINED,
        ]);
        $this->assertName($staticString, [
            self::KEY_PROPERTY => 'staticString',
            self::KEY_TYPE => 'string',
        ]);
        $this->assertName($staticNullableString, [
            self::KEY_PROPERTY => 'staticNullableString',
            self::KEY_TYPE => 'string',
        ]);
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
            $this->assertSame($val, $property->$key, '@OA\Property()->property based on propertyname');
        }
    }
}
