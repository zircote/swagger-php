<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Type;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Context;
use OpenApi\Generator;
use OpenApi\Pipeline;
use OpenApi\Processors\AugmentSchemas;
use OpenApi\Processors\MergeIntoOpenApi;
use OpenApi\Tests\Fixtures\PHP\DocblockAndTypehintTypes;
use OpenApi\Tests\OpenApiTestCase;
use OpenApi\TypeResolverInterface;
use PHPUnit\Framework\Attributes\DataProvider;

class TypeResolverTest extends OpenApiTestCase
{
    public static function resolverAugmentCases(): iterable
    {
        $expectations = [
            OA\OpenApi::VERSION_3_0_0 => [
                'nothing' => '{ "property": "nothing" }',
                'string' => '{ "type": "string", "property": "string" }',
                'nullablestring' => '{ "type": "string", "nullable": true, "property": "nullableString" }',
                'nullablestringexplicit' => '{ "type": "string", "nullable": false, "property": "nullableStringExplicit" }',
                'nullablestringdocblock' => '{ "type": "string", "nullable": true, "property": "nullableStringDocblock" }',
                'nullablestringnative' => '{ "type": "string", "nullable": true, "property": "nullableStringNative" }',
                'stringarray' => '{ "type": "array", "items": { "type": "string" }, "property": "stringArray" }',
                'stringlist' => '{ "type": "array", "items": { "type": "string" }, "property": "stringList" }',
                'stringlistexplicit' => '{ "type": "array", "items": { "type": "string", "example": "foo" }, "property": "stringListExplicit" }',
                'nullablestringlist' => '{ "type": "array", "items": { "type": "string" }, "nullable": true, "property": "nullableStringList" }',
                'nullablestringlistunion' => '{ "type": "array", "items": { "type": "string" }, "nullable": true, "property": "nullableStringListUnion" }',
                'class' => '{ "$ref": "#/components/schemas/DocblockAndTypehintTypes" }',
                'nullableclass' => '{ "oneOf": [ { "$ref": "#/components/schemas/DocblockAndTypehintTypes" } ], "nullable": true, "property": "nullableClass" }',
                'namespacedglobalclass' => '{ "type": "string", "format": "date-time", "property": "namespacedGlobalClass" }',
                'nullablenamespacedglobalclass' => '{ "type": "string", "format": "date-time", "nullable": true, "property": "nullableNamespacedGlobalClass" }',
                'alsonullablenamespacedglobalclass' => '{ "type": "string", "format": "date-time", "nullable": true, "property": "alsoNullableNamespacedGlobalClass" }',
                'intrange' => '{ "type": "integer", "maximum": 10, "minimum": -9223372036854775808, "property": "intRange" }',
                'positiveint' => '{ "type": "integer", "maximum": 9223372036854775807, "minimum": 1, "property": "positiveInt" }',
                'nonzeroint' => '{ "type": "integer", "not": { "enum": [ 0 ] }, "property": "nonZeroInt" }',
                'arrayshape' => '{ "type": "array", "items": { "type": "boolean" }, "property": "arrayShape" }',
                'uniontype' => '{ "property": "unionType" }',
                'promotedstring' => '{ "type": "string", "property": "promotedString" }',
                'legacy:mixedunion' => '{ "example": "My value", "property": "mixedUnion" }',
                'type-info:mixedunion' => '{ "example": "My value", "oneOf": [ { "type": "string" }, { "type": "array", "items": { "type": "mixed" } } ], "property": "mixedUnion" }',
                'getstring' => '{ "type": "string", "property": "getString" }',
                'paramdatetimelist' => '{ "type": "array", "items": { "type": "string", "format": "date-time" }, "property": "paramDateTimeList" }',
                'paramstringlist' => '{ "type": "array", "items": { "type": "string" }, "property": "paramStringList" }',
                'blah' => '{ "type": "string", "example": "My blah", "nullable": true, "property": "blah" }',
                'blah_values' => '{ "type": "array", "items": { "type": "string", "example": "hello" }, "nullable": true, "property": "blah_values" }',
                'oneofvar' => '{ "oneOf": [ { "type": "string" }, { "type": "bool" } ], "property": "oneOfVar" }',
                'oneoflist' => '{ "type": "array", "items": { "oneOf": [ { "type": "string" }, { "type": "bool" } ] }, "property": "oneOfList" }',
                'legacy:nullabletypedlistunion' => '{ "nullable": true, "property": "nullableTypedListUnion" }',
                'type-info:nullabletypedlistunion' => '{ "nullable": true, "oneOf": [ { "$ref": "#/components/schemas/DocblockAndTypehintTypes" }, { "type": "array", "items": { "$ref": "#/components/schemas/DocblockAndTypehintTypes" } } ], "property": "nullableTypedListUnion" }',
                'legacy:nullablenestedtypedlistunion' => '{ "nullable": true, "property": "nullableNestedTypedListUnion" }',
                'type-info:nullablenestedtypedlistunion' => '{ "nullable": true, "oneOf": [ { "$ref": "#/components/schemas/DocblockAndTypehintTypes" }, { "type": "array", "items": { "$ref": "#/components/schemas/DocblockAndTypehintTypes" } }, { "type": "array", "items": { "type": "array", "items": { "$ref": "#/components/schemas/DocblockAndTypehintTypes" } } } ], "property": "nullableNestedTypedListUnion" }',
                'reflectionvalue' => '{ "example": true, "nullable": true, "property": "reflectionValue" }',
                'legacy:intersectionvar' => '{ "property": "intersectionVar" }',
                'type-info:intersectionvar' => '{ "allOf": [ { "$ref": "#/components/schemas/FirstInterface" }, { "$ref": "#/components/schemas/SecondInterface" } ], "property": "intersectionVar" }',
                'legacy:nestedoneof' => '{ "property": "nestedOneOf" }',
                'type-info:nestedoneof' => '{ "oneOf": [ { "type": "array", "items": { "$ref": "#/components/schemas/DocblockAndTypehintTypes" } }, { "type": "array", "items": { "type": "string" } } ], "property": "nestedOneOf" }',
                'legacy:nestedoneofwithitems' => '{ "type": "array", "items": { "oneOf": [ { "$ref": "#/components/schemas/DocblockAndTypehintTypes" }, { "type": "string" } ] }, "property": "nestedOneOfWithItems" }',
                'type-info:nestedoneofwithitems' => '{ "type": "array", "items": { "oneOf": [ { "$ref": "#/components/schemas/DocblockAndTypehintTypes" }, { "type": "string" } ] }, "property": "nestedOneOfWithItems" }',
            ],
            OA\OpenApi::VERSION_3_1_0 => [
                'nothing' => '{ "property": "nothing" }',
                'string' => '{ "type": "string", "property": "string" }',
                'nullablestring' => '{ "type": [ "string", "null" ], "property": "nullableString" }',
                'nullablestringexplicit' => '{ "type": "string", "property": "nullableStringExplicit" }',
                'nullablestringdocblock' => '{ "type": [ "string", "null" ], "property": "nullableStringDocblock" }',
                'nullablestringnative' => '{ "type": [ "string", "null" ], "property": "nullableStringNative" }',
                'stringarray' => '{ "type": "array", "items": { "type": "string" }, "property": "stringArray" }',
                'stringlist' => '{ "type": "array", "items": { "type": "string" }, "property": "stringList" }',
                'stringlistexplicit' => '{ "type": "array", "items": { "type": "string", "example": "foo" }, "property": "stringListExplicit" }',
                'nullablestringlist' => '{ "type": [ "array", "null" ], "items": { "type": "string" }, "property": "nullableStringList" }',
                'nullablestringlistunion' => '{ "type": [ "array", "null" ], "items": { "type": "string" }, "property": "nullableStringListUnion" }',
                'class' => '{ "$ref": "#/components/schemas/DocblockAndTypehintTypes" }',
                'nullableclass' => '{ "oneOf": [ { "$ref": "#/components/schemas/DocblockAndTypehintTypes" }, { "type": "null" } ], "property": "nullableClass" }',
                'namespacedglobalclass' => '{ "type": "string", "format": "date-time", "property": "namespacedGlobalClass" }',
                'nullablenamespacedglobalclass' => '{ "type": [ "string", "null" ], "format": "date-time", "property": "nullableNamespacedGlobalClass" }',
                'alsonullablenamespacedglobalclass' => '{ "type": [ "string", "null" ], "format": "date-time", "property": "alsoNullableNamespacedGlobalClass" }',
                'intrange' => '{ "type": "integer", "maximum": 10, "minimum": -9223372036854775808, "property": "intRange" }',
                'positiveint' => '{ "type": "integer", "maximum": 9223372036854775807, "minimum": 1, "property": "positiveInt" }',
                'nonzeroint' => '{ "type": "integer", "not": { "const": 0 }, "property": "nonZeroInt" } ',
                'arrayshape' => '{ "type": "array", "items": { "type": "boolean" }, "property": "arrayShape" }',
                'legacy:uniontype' => '{ "property": "unionType" }',
                'type-info:uniontype' => '{ "type": [ "integer", "string" ], "property": "unionType" }',
                'promotedstring' => '{ "type": "string", "property": "promotedString" }',
                'legacy:mixedunion' => '{ "example": "My value", "property": "mixedUnion" }',
                'type-info:mixedunion' => '{ "example": "My value", "oneOf": [ { "type": [ "string" ] }, { "type": "array", "items": { "type": "mixed" } } ], "property": "mixedUnion" }',
                'getstring' => '{ "type": "string", "property": "getString" }',
                'paramdatetimelist' => '{ "type": "array", "items": { "type": "string", "format": "date-time" }, "property": "paramDateTimeList" }',
                'paramstringlist' => '{ "type": "array", "items": { "type": "string" }, "property": "paramStringList" }',
                'blah' => '{ "type": [ "string", "null" ], "example": "My blah", "property": "blah" }',
                'blah_values' => '{ "type": [ "array", "null" ], "items": { "type": "string", "example": "hello" }, "property": "blah_values" }',
                'oneofvar' => '{ "oneOf": [ { "type": "string" }, { "type": "bool" } ], "property": "oneOfVar" }',
                'oneoflist' => '{ "type": "array", "items": { "oneOf": [ { "type": "string" }, { "type": "bool" } ] }, "property": "oneOfList" }',
                'legacy:nullabletypedlistunion' => '{ "property": "nullableTypedListUnion" }',
                'type-info:nullabletypedlistunion' => '{ "oneOf": [ { "$ref": "#/components/schemas/DocblockAndTypehintTypes" }, { "type": "array", "items": { "$ref": "#/components/schemas/DocblockAndTypehintTypes" } }, { "type": "null" } ], "property": "nullableTypedListUnion" }',
                'legacy:nullablenestedtypedlistunion' => '{ "property": "nullableNestedTypedListUnion" }',
                'type-info:nullablenestedtypedlistunion' => '{ "oneOf": [ { "$ref": "#/components/schemas/DocblockAndTypehintTypes" }, { "type": "array", "items": { "$ref": "#/components/schemas/DocblockAndTypehintTypes" } }, { "type": "array", "items": { "type": "array", "items": { "$ref": "#/components/schemas/DocblockAndTypehintTypes" } } }, { "type": "null" } ], "property": "nullableNestedTypedListUnion" }',
                'legacy:reflectionvalue' => '{ "example": true, "property": "reflectionValue" }',
                'type-info:reflectionvalue' => '{ "type": [ "boolean", "integer", "null" ], "example": true, "property": "reflectionValue" }',
                'legacy:intersectionvar' => '{ "property": "intersectionVar" }',
                'type-info:intersectionvar' => '{ "allOf": [ { "$ref": "#/components/schemas/FirstInterface" }, { "$ref": "#/components/schemas/SecondInterface" } ], "property": "intersectionVar" }',
                'legacy:nestedoneof' => '{ "property": "nestedOneOf" }',
                'type-info:nestedoneof' => '{ "oneOf": [ { "type": "array", "items": { "$ref": "#/components/schemas/DocblockAndTypehintTypes" } }, { "type": "array", "items": { "type": "string" } } ], "property": "nestedOneOf" }',
                'legacy:nestedoneofwithitems' => '{ "type": "array", "items": { "oneOf": [ { "$ref": "#/components/schemas/DocblockAndTypehintTypes" }, { "type": "string" } ] }, "property": "nestedOneOfWithItems" }',
                'type-info:nestedoneofwithitems' => '{ "type": "array", "items": { "oneOf": [ { "$ref": "#/components/schemas/DocblockAndTypehintTypes" }, { "type": "string" } ] }, "property": "nestedOneOfWithItems" }',
            ],
        ];

        $rc = new \ReflectionClass(DocblockAndTypehintTypes::class);
        $fixtureFolder = dirname($rc->getFileName());
        $sources = [
            $rc->getFileName(),
            "$fixtureFolder/FirstInterface.php",
            "$fixtureFolder/SecondInterface.php",
        ];

        foreach (static::getTypeResolvers() as $key => $typeResolver) {
            foreach ([OA\OpenApi::VERSION_3_0_0, OA\OpenApi::VERSION_3_1_0] as $version) {
                $analysis = (new Generator())
                    ->setVersion($version)
                    ->setProcessorPipeline(new Pipeline([new MergeIntoOpenApi(), new AugmentSchemas()]))
                    ->withContext(function (Generator $generator, Analysis $analysis, Context $context) use ($sources) {
                        $generator->generate($sources, $analysis, false);

                        return $analysis;
                    });

                $schema = $analysis->getAnnotationForSource(DocblockAndTypehintTypes::class);

                foreach ($schema->properties as $ii => $property) {
                    $property->property = $property->_context->property
                        // promoted properties might not have a name!
                        ?? $property->_context->method;

                    $caseName = strtolower($property->property);
                    $resolverCaseName = "$key:$caseName";
                    $fullCase = "$key:{$version}[$ii]-$caseName";

                    $json = $expectations[$version][$caseName] ?? $expectations[$version][$resolverCaseName] ?? null;

                    if ($json) {
                        yield $fullCase => [
                            $typeResolver,
                            $analysis,
                            $property,
                            json_decode($json, true),
                        ];
                    }
                }
            }
        }
    }

    #[DataProvider('resolverAugmentCases')]
    public function testAugmentSchemaType(TypeResolverInterface $typeResolver, Analysis $analysis, OA\Schema $schema, array $expected): void
    {
        $typeResolver->augmentSchemaType($analysis, $schema);

        $this->assertSpecEquals($schema->toJson(), $expected, $schema->toJson());
    }
}
