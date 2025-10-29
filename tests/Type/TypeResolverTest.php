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

/**
 * @requires PHP 8.1
 */
class TypeResolverTest extends OpenApiTestCase
{
    public static function resolverAugmentCases(): iterable
    {
        $rc = new \ReflectionClass(DocblockAndTypehintTypes::class);

        $typeResolvers = ['legacy' => new LegacyTypeResolver()];
        if (class_exists(StringTypeResolver::class)) {
            $typeResolvers['type-info'] = new TypeInfoTypeResolver();
        }

        $expectations = [
            'nothing' => '{ "property": "nothing" }',
            'string' => '{ "type": "string", "property": "string" }',
            'nullablestring' => '{ "type": "string", "nullable": true, "property": "nullableString" }',
            'stringarray' => '{ "type": "array", "items": { "type": "string" }, "property": "stringArray" }',
            'stringlist' => '{ "type": "array", "items": { "type": "string" }, "property": "stringList" }',
            'nullablestringlist' => '{ "type": "array", "items": { "type": "string" }, "nullable": true, "property": "nullableStringList" }',
            'nullablestringlistunion' => '{ "type": "array", "items": { "type": "string" }, "nullable": true, "property": "nullableStringListUnion" }',
            'class' => '{ "$ref": "#/components/schemas/DocblockAndTypehintTypes" }',
            'nullableclass' => '{ "oneOf": [ { "$ref": "#/components/schemas/DocblockAndTypehintTypes" } ], "nullable": true, "property": "nullableClass" }',
            'namespacedglobalclass' => '{ "type": "string", "format": "date-time", "property": "namespacedGlobalClass" }',
            'nullablenamespacedglobalclass' => '{ "type": "string", "format": "date-time", "nullable": true, "property": "nullableNamespacedGlobalClass" }',
            'alsonullablenamespacedglobalclass' => '{ "type": "string", "format": "date-time", "nullable": true, "property": "alsoNullableNamespacedGlobalClass" }',
            'intrange' => '{ "type": "integer", "property": "intRange" }',
            'positiveint' => '{ "type": "integer", "property": "positiveInt" }',
            'nonzeroint' => '{ "type": "integer", "property": "nonZeroInt" }',
            'arrayshape' => '{ "type": "array", "items": { "type": "bool" }, "property": "arrayShape" }',
            'uniontype' => '{ "property": "unionType" }',
            'promotedstring' => '{ "type": "string", "property": "promotedString" }',
            'getstring' => '{ "type": "string", "property": "getString" }',
        ];

        foreach ($typeResolvers as $key => $typeResolver) {
            $analysis = (new Generator())
                ->setProcessorPipeline(new Pipeline([new MergeIntoOpenApi(), new AugmentSchemas()]))
                ->withContext(function (Generator $generator, Analysis $analysis, Context $context) use ($rc) {
                    $generator->generate([$rc->getFileName()], $analysis, false);

                    return $analysis;
                });

            $schema = $analysis->getSchemaForSource(DocblockAndTypehintTypes::class);

            foreach ($schema->properties as $property) {
                if (Generator::isDefault($property->property)) {
                    $property->property = $property->_context->property;
                }

                $context = $property->_context;
                $caseName = strtolower($property->property ?? $context->function ?? $context->method ?? $context->class ?? '');
                $case = "$key-$caseName";

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
