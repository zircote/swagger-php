<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Processors;

use OpenApi\Annotations as OA;
use OpenApi\Processors\AugmentProperties;
use OpenApi\Processors\AugmentSchemas;
use OpenApi\Processors\MergeIntoComponents;
use OpenApi\Processors\MergeIntoOpenApi;
use OpenApi\Tests\OpenApiTestCase;
use OpenApi\Undefined;
use PHPUnit\Framework\Attributes\Group;

#[Group('Properties')]
final class AugmentPropertiesTest extends OpenApiTestCase
{
    public function testAugmentProperties(): void
    {
        $analysis = $this->analysisFromFixtures([
            'Customer.php',
        ], $this->processorPipeline([
            new MergeIntoOpenApi(),
            new MergeIntoComponents(),
            new AugmentSchemas(),
        ]));

        $customer = $analysis->openapi->components->schemas[0];

        [
            $firstName,
            $secondName,
            $thirdName,
            $fourthName,
            $iq,
            $lastName,
            $tags,
            $submittedBy,
            $friends,
            $bestFriend,
            $endorsedFriends
        ] = $customer->properties;

        // Verify no values where defined in the annotation.
        $this->assertSame(Undefined::UNDEFINED, $firstName->property);
        $this->assertSame(Undefined::UNDEFINED, $firstName->description);
        $this->assertSame(Undefined::UNDEFINED, $firstName->type);

        $this->assertSame(Undefined::UNDEFINED, $lastName->property);
        $this->assertSame(Undefined::UNDEFINED, $lastName->description);
        $this->assertSame(Undefined::UNDEFINED, $lastName->type);

        $this->assertSame(Undefined::UNDEFINED, $tags->property);
        $this->assertSame(Undefined::UNDEFINED, $tags->type);
        $this->assertSame(Undefined::UNDEFINED, $tags->items);

        $this->assertSame(Undefined::UNDEFINED, $submittedBy->property);
        $this->assertSame(Undefined::UNDEFINED, $submittedBy->ref);

        $this->assertSame(Undefined::UNDEFINED, $friends->property);
        $this->assertSame(Undefined::UNDEFINED, $friends->type);

        $this->assertSame(Undefined::UNDEFINED, $bestFriend->property);
        $this->assertSame(Undefined::UNDEFINED, $bestFriend->nullable);
        $this->assertSame(Undefined::UNDEFINED, $bestFriend->allOf);

        $this->assertSame(Undefined::UNDEFINED, $endorsedFriends->property);
        $this->assertSame(Undefined::UNDEFINED, $endorsedFriends->nullable);
        $this->assertSame(Undefined::UNDEFINED, $endorsedFriends->allOf);

        $this->processorPipeline([new AugmentProperties()])->process($analysis);

        $expectedValues = [
            'property' => 'firstname',
            'example' => 'John',
            'description' => 'The first name of the customer.',
            'type' => 'string',
        ];
        $this->assertName($firstName, $expectedValues);

        $expectedValues = [
            'property' => 'secondname',
            'example' => 'Allan',
            'description' => 'the second name of the customer',
            'type' => 'string',
            'nullable' => true,
        ];
        $this->assertName($secondName, $expectedValues);

        $expectedValues = [
            'property' => 'thirdname',
            'example' => 'Peter',
            'description' => 'The third name of the customer.',
            'type' => 'string',
            'nullable' => true,
        ];
        $this->assertName($thirdName, $expectedValues);

        $expectedValues = [
            'property' => 'fourthname',
            'example' => 'Unknown',
            'description' => 'The unknown name of the customer.',
            'type' => Undefined::UNDEFINED,
            'nullable' => Undefined::UNDEFINED,
        ];
        $this->assertName($fourthName, $expectedValues);

        $expectedValues = [
            'property' => 'iq',
            'example' => '0.00',
            'description' => Undefined::UNDEFINED,
            'type' => 'string',
            'nullable' => false,
        ];

        $this->assertName($iq, $expectedValues);

        $expectedValues = [
            'property' => 'lastname',
            'example' => Undefined::UNDEFINED,
            'description' => 'the lastname of the customer',
            'type' => 'string',
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
        $this->assertSame('#/components/schemas/Customer', $bestFriend->ref);

        $this->assertSame('endorsedFriends', $endorsedFriends->property);
        $this->assertSame('array', $endorsedFriends->type);
        $this->assertTrue($endorsedFriends->nullable);
        $this->assertSame('#/components/schemas/Customer', $endorsedFriends->items->ref);
    }

    public function testTypedProperties(): void
    {
        $analysis = $this->analysisFromFixtures([
            'TypedProperties.php',
        ], $this->processorPipeline([
            new MergeIntoOpenApi(),
            new MergeIntoComponents(),
            new AugmentSchemas(),
        ]));

        [
            $stringType,
            $intType,
            $nullableString,
            $arrayType,
            $dateTime,
            $dateTimeTimestamp,
            $qualified,
            $namespaced,
            $importedNamespace,
            $varTrumpsNative,
            $annotationTrumpsNative,
            $annotationTrumpsAll,
            $undefined,
            $onlyAnnotated,
            $onlyVar,
            $staticUndefined,
            $staticString,
            $staticNullableString,
            $nativeArray,
            $stringMap,
            $arrayShape,
            $unmappableMap,
            $mixedMap,
            $mixedValue,
        ] = $analysis->openapi->components->schemas[0]->properties;

        $this->assertName($stringType, [
            'property' => Undefined::UNDEFINED,
            'type' => Undefined::UNDEFINED,
        ]);
        $this->assertName($intType, [
            'property' => Undefined::UNDEFINED,
            'type' => Undefined::UNDEFINED,
        ]);
        $this->assertName($nullableString, [
            'property' => Undefined::UNDEFINED,
            'type' => Undefined::UNDEFINED,
        ]);
        $this->assertName($arrayType, [
            'property' => Undefined::UNDEFINED,
            'type' => Undefined::UNDEFINED,
        ]);
        $this->assertName($dateTime, [
            'property' => Undefined::UNDEFINED,
            'type' => Undefined::UNDEFINED,
        ]);
        $this->assertName($dateTimeTimestamp, [
            'property' => Undefined::UNDEFINED,
            'type' => 'integer',
        ]);
        $this->assertName($qualified, [
            'property' => Undefined::UNDEFINED,
            'type' => Undefined::UNDEFINED,
        ]);
        $this->assertName($namespaced, [
            'property' => Undefined::UNDEFINED,
            'type' => Undefined::UNDEFINED,
        ]);
        $this->assertName($importedNamespace, [
            'property' => Undefined::UNDEFINED,
            'type' => Undefined::UNDEFINED,
        ]);
        $this->assertName($varTrumpsNative, [
            'property' => Undefined::UNDEFINED,
            'type' => Undefined::UNDEFINED,
        ]);
        $this->assertName($annotationTrumpsNative, [
            'property' => Undefined::UNDEFINED,
            'type' => 'integer',
        ]);
        $this->assertName($annotationTrumpsAll, [
            'property' => Undefined::UNDEFINED,
            'type' => 'integer',
        ]);
        $this->assertName($undefined, [
            'property' => Undefined::UNDEFINED,
            'type' => Undefined::UNDEFINED,
        ]);
        $this->assertName($onlyAnnotated, [
            'property' => Undefined::UNDEFINED,
            'type' => 'integer',
        ]);
        $this->assertName($onlyVar, [
            'property' => Undefined::UNDEFINED,
            'type' => Undefined::UNDEFINED,
        ]);
        $this->assertName($staticUndefined, [
            'property' => Undefined::UNDEFINED,
            'type' => Undefined::UNDEFINED,
        ]);
        $this->assertName($staticString, [
            'property' => Undefined::UNDEFINED,
            'type' => Undefined::UNDEFINED,
        ]);
        $this->assertName($staticNullableString, [
            'property' => Undefined::UNDEFINED,
            'type' => Undefined::UNDEFINED,
        ]);
        $this->assertName($nativeArray, [
            'property' => Undefined::UNDEFINED,
            'type' => Undefined::UNDEFINED,
        ]);
        $this->assertName($stringMap, [
            'property' => Undefined::UNDEFINED,
            'type' => Undefined::UNDEFINED,
        ]);
        $this->assertName($unmappableMap, [
            'property' => Undefined::UNDEFINED,
            'type' => Undefined::UNDEFINED,
        ]);
        $this->assertName($mixedMap, [
            'property' => Undefined::UNDEFINED,
            'type' => Undefined::UNDEFINED,
        ]);
        $this->assertName($mixedValue, [
            'property' => Undefined::UNDEFINED,
            'type' => Undefined::UNDEFINED,
        ]);

        $this->processorPipeline([new AugmentProperties()])->process($analysis);

        $this->assertName($stringType, [
            'property' => 'stringType',
            'type' => 'string',
        ]);
        $this->assertName($intType, [
            'property' => 'intType',
            'type' => 'integer',
        ]);
        $this->assertName($nullableString, [
            'property' => 'nullableString',
            'type' => 'string',
        ]);
        $this->assertName($arrayType, [
            'property' => 'arrayType',
            'type' => 'array',
        ]);
        $this->assertIsObject($arrayType->items);
        $this->assertTrue(property_exists($arrayType->items, 'ref'));
        $this->assertEquals(
            '#/components/schemas/TypedProperties',
            $arrayType->items->ref
        );
        $this->assertName($dateTime, [
            'property' => 'dateTime',
            'type' => 'string',
            'format' => 'date-time',
        ]);
        $this->assertName($dateTimeTimestamp, [
            'property' => 'dateTimeTimestamp',
            'type' => 'integer',
        ]);
        $this->assertName($qualified, [
            'property' => 'qualified',
            'type' => 'string',
            'format' => 'date-time',
        ]);
        $this->assertName($namespaced, [
            'property' => 'namespaced',
            'ref' => '#/components/schemas/TypedProperties',
        ]);
        $this->assertName($importedNamespace, [
            'property' => 'importedNamespace',
            'ref' => '#/components/schemas/TypedProperties',
        ]);
        $this->assertName($varTrumpsNative, [
            'property' => 'varTrumpsNative',
            'type' => 'integer',
        ]);
        $this->assertName($annotationTrumpsNative, [
            'property' => 'annotationTrumpsNative',
            'type' => 'integer',
        ]);
        $this->assertName($annotationTrumpsAll, [
            'property' => 'annotationTrumpsAll',
            'type' => 'integer',
        ]);
        $this->assertName($undefined, [
            'property' => 'undefined',
            'type' => Undefined::UNDEFINED,
        ]);
        $this->assertName($onlyAnnotated, [
            'property' => 'onlyAnnotated',
            'type' => 'integer',
        ]);
        $this->assertName($onlyVar, [
            'property' => 'onlyVar',
            'type' => 'integer',
        ]);
        $this->assertName($staticUndefined, [
            'property' => 'staticUndefined',
            'type' => Undefined::UNDEFINED,
        ]);
        $this->assertName($staticString, [
            'property' => 'staticString',
            'type' => 'string',
        ]);
        $this->assertName($staticNullableString, [
            'property' => 'staticNullableString',
            'type' => 'string',
        ]);
        $this->assertName($nativeArray, [
            'property' => 'nativeArray',
            'type' => 'array',
        ]);
        $this->assertIsObject($nativeArray->items);
        $this->assertTrue(property_exists($nativeArray->items, 'ref'));
        $this->assertEquals(
            'string',
            $nativeArray->items->type
        );
        $this->assertName($stringMap, [
            'property' => 'stringMap',
            'type' => 'object',
        ]);
        $this->assertFalse(Undefined::isDefault($stringMap->additionalProperties));
        $this->assertSame('string', $stringMap->additionalProperties->type);
        $this->assertTrue(Undefined::isDefault($stringMap->items));

        $this->assertName($arrayShape, [
            'property' => 'arrayShape',
            'type' => 'object',
        ]);
        $this->assertTrue(Undefined::isDefault($arrayShape->additionalProperties));
        $this->assertSame(['id'], $arrayShape->required);
        [$id, $name] = $arrayShape->properties;
        $this->assertName($id, ['property' => 'id', 'type' => 'integer']);
        $this->assertName($name, ['property' => 'name', 'type' => 'string']);

        $this->assertName($unmappableMap, [
            'property' => 'unmappableMap',
            'type' => 'object',
        ]);
        $this->assertFalse(Undefined::isDefault($unmappableMap->additionalProperties));
        $this->assertTrue(
            Undefined::isDefault($unmappableMap->additionalProperties->type),
            'callable has no OpenAPI representation, so additionalProperties stays open instead of emitting type: callable',
        );

        $this->assertName($mixedMap, [
            'property' => 'mixedMap',
            'type' => 'object',
        ]);
        $this->assertFalse(Undefined::isDefault($mixedMap->additionalProperties));
        $this->assertTrue(
            Undefined::isDefault($mixedMap->additionalProperties->type),
            'mixed has no OpenAPI representation, so additionalProperties stays open instead of emitting type: mixed',
        );

        $this->assertName($mixedValue, [
            'property' => 'mixedValue',
        ]);
        $this->assertTrue(
            Undefined::isDefault($mixedValue->type),
            'mixed is the OpenAPI any value, so the property stays an open schema with no type instead of emitting type: mixed',
        );
    }

    public function testComplexVarTypeDescription(): void
    {
        $analysis = $this->analysisFromFixtures([
            'ComplexVarTypes.php',
        ], $this->processorPipeline([
            new MergeIntoOpenApi(),
            new MergeIntoComponents(),
            new AugmentSchemas(),
            new AugmentProperties(),
        ]));

        [
            $map,
            $userMap,
            $inlineGenericDesc,
            $namespacedMap,
            $intList,
            $arrayOrStringList,
            $nullableMap,
            $mixedUserList,
            $nullableInlineDesc
        ] = $analysis->openapi->components->schemas[0]->properties;

        // Description should come from docblock text, not the generic type fragment
        $this->assertSame('An associative array with string values.', $map->description);
        $this->assertSame('A map from int to user objects.', $userMap->description);
        $this->assertSame('Inline generic with description', $inlineGenericDesc->description);
        $this->assertSame('A map using namespaced class.', $namespacedMap->description);
        $this->assertSame('List of integer IDs.', $intList->description);
        $this->assertSame('Either an array or a string list.', $arrayOrStringList->description);
        $this->assertSame('Nullable map of strings.', $nullableMap->description);
        $this->assertSame('A collection of users or a single user array.', $mixedUserList->description);
        $this->assertSame('Nullable inline with description', $nullableInlineDesc->description);
    }

    protected function assertName(OA\Property $property, array $expectedValues): void
    {
        foreach ($expectedValues as $key => $val) {
            $this->assertSame($val, $property->$key, '@OA\Property()->property based on propertyname');
        }
    }
}
