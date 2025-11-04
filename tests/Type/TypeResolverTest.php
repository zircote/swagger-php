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
use OpenApi\Type\LegacyTypeResolver;
use OpenApi\Type\TypeInfoTypeResolver;
use OpenApi\TypeResolverInterface;
use Radebatz\TypeInfoExtras\TypeResolver\StringTypeResolver;

class TypeResolverTest extends OpenApiTestCase
{
    public static function resolverAugmentCases(): iterable
    {
        if (\PHP_VERSION_ID < 80100) {
            return [];
        }

        $expectations = [
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
            'mixedunion' => '{ "example": "My value", "property": "mixedUnion" }',
            'getstring' => '{ "type": "string", "property": "getString" }',
            'paramdatetimelist' => '{ "type": "array", "items": { "type": "string", "format": "date-time" }, "property": "paramDateTimeList" }',
            'paramstringlist' => '{ "type": "array", "items": { "type": "string" }, "property": "paramStringList" }',
            'blah' => '{ "type": "string", "example": "My blah", "nullable": true, "property": "blah" }',
            'blah_values' => '{ "type": "array", "items": { "type": "string", "example": "hello" }, "nullable": true, "property": "blah_values" }',
            'oneofvar' => '{ "oneOf": [ { "type": "string" }, { "type": "bool" } ], "property": "oneOfVar" }',
            'oneoflist' => '{ "type": "array", "items": { "oneOf": [ { "type": "string" }, { "type": "bool" } ] }, "property": "oneOfList" }',
        ];

        $rc = new \ReflectionClass(DocblockAndTypehintTypes::class);

        $typeResolvers = ['legacy' => new LegacyTypeResolver()];
        if (class_exists(StringTypeResolver::class)) {
            $typeResolvers['type-info'] = new TypeInfoTypeResolver();
        }

        foreach ($typeResolvers as $key => $typeResolver) {
            $analysis = (new Generator())
                ->setProcessorPipeline(new Pipeline([new MergeIntoOpenApi(), new AugmentSchemas()]))
                ->withContext(function (Generator $generator, Analysis $analysis, Context $context) use ($rc) {
                    $generator->generate([$rc->getFileName()], $analysis, false);

                    return $analysis;
                });

            $schema = $analysis->getAnnotationForSource(DocblockAndTypehintTypes::class);

            foreach ($schema->properties as $ii => $property) {
                $property->property = $property->_context->property
                    // promoted properties might not have a name!
                    ?? $property->_context->method;

                $caseName = strtolower($property->property);
                $case = "$key-[$ii]-$caseName";

                yield $case => [
                    $typeResolver,
                    $analysis,
                    $property,
                    json_decode($expectations[$caseName] ?? $expectations[$case] ?? '{}', true),
                ];
            }
        }
    }

    /**
     * @dataProvider resolverAugmentCases
     */
    public function testAugmentSchemaType(TypeResolverInterface $typeResolver, Analysis $analysis, OA\Schema $schema, array $expected): void
    {
        $typeResolver->augmentSchemaType($analysis, $schema);

        $this->assertSpecEquals($schema->toJson(), $expected, $schema->toJson());
    }
}
