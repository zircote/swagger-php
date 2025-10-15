<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Type;

use OpenApi\Analysis;
use OpenApi\Context;
use OpenApi\Generator;
use OpenApi\Tests\Fixtures\PHP\DocblockAndTypehintTypes;
use OpenApi\Tests\OpenApiTestCase;
use OpenApi\Type\LegacyTypeResolver;
use OpenApi\Type\TypeInfoTypeResolver;
use OpenApi\TypeResolverInterface;

/**
 * @requires PHP 8.1
 */
class TypeResolverTest extends OpenApiTestCase
{
    public static function resolverPropertyCases(): iterable
    {
        $rc = new \ReflectionClass(DocblockAndTypehintTypes::class);
        $analysis = (new Generator())
            ->withContext(function (Generator $generator, Analysis $analysis, Context $context) use ($rc) {
                $generator->generate([$rc->getFileName()], $analysis, false);

                return $analysis;
            });

        $schema = $analysis->getSchemaForSource(DocblockAndTypehintTypes::class);
        $context = $schema->_context;

        $resolvers = ['legacy' => new LegacyTypeResolver($context)];
        if (class_exists('Radebatz\TypeInfoExtras\TypeResolver\StringTypeResolver')) {
            $resolvers['type-info'] = new TypeInfoTypeResolver();
        }

        foreach ($resolvers as $key => $typeResolver) {
            yield "$key-nothing" => [
                $typeResolver,
                new \ReflectionProperty(DocblockAndTypehintTypes::class, 'nothing'),
                [
                    'reflection' => ['explicitType' => null, 'explicitDetails' => null, 'types' => [], 'name' => 'nothing', 'nullable' => true, 'isArray' => false],
                    'docblock' => ['explicitType' => null, 'explicitDetails' => null, 'types' => [], 'name' => 'nothing', 'nullable' => true, 'isArray' => false],
                ],
            ];

            yield "$key-string" => [
                $typeResolver,
                new \ReflectionProperty(DocblockAndTypehintTypes::class, 'string'),
                [
                    'reflection' => ['explicitType' => 'string', 'explicitDetails' => null, 'types' => ['string'], 'name' => 'string', 'nullable' => false, 'isArray' => false],
                    'docblock' => ['explicitType' => 'string', 'explicitDetails' => null, 'types' => ['string'], 'name' => 'string', 'nullable' => false, 'isArray' => false],
                ],
            ];

            yield "$key-?string" => [
                $typeResolver,
                new \ReflectionProperty(DocblockAndTypehintTypes::class, 'nullableString'),
                [
                    'reflection' => ['explicitType' => 'string', 'explicitDetails' => null, 'types' => ['string'], 'name' => 'nullableString', 'nullable' => true, 'isArray' => false],
                    'docblock' => ['explicitType' => 'string', 'explicitDetails' => null, 'types' => ['string'], 'name' => 'nullableString', 'nullable' => true, 'isArray' => false],
                ],
            ];

            yield "$key-string[]" => [
                $typeResolver,
                new \ReflectionProperty(DocblockAndTypehintTypes::class, 'stringArray'),
                [
                    'reflection' => ['explicitType' => 'mixed', 'explicitDetails' => null, 'types' => ['mixed'], 'name' => 'stringArray', 'nullable' => false, 'isArray' => true],
                    'docblock' => ['explicitType' => 'string', 'explicitDetails' => null, 'types' => ['string'], 'name' => 'stringArray', 'nullable' => false, 'isArray' => true],
                ],
            ];

            yield "$key-array<string>" => [
                $typeResolver,
                new \ReflectionProperty(DocblockAndTypehintTypes::class, 'stringList'),
                [
                    'reflection' => ['explicitType' => 'mixed', 'explicitDetails' => null, 'types' => ['mixed'], 'name' => 'stringList', 'nullable' => false, 'isArray' => true],
                    'docblock' => ['explicitType' => 'string', 'explicitDetails' => null, 'types' => ['string'], 'name' => 'stringList', 'nullable' => false, 'isArray' => true],
                ],
            ];

            yield "$key-?array<string>" => [
                $typeResolver,
                new \ReflectionProperty(DocblockAndTypehintTypes::class, 'nullableStringList'),
                [
                    'reflection' => ['explicitType' => 'mixed', 'explicitDetails' => null, 'types' => ['mixed'], 'name' => 'nullableStringList', 'nullable' => true, 'isArray' => true],
                    'docblock' => ['explicitType' => 'string', 'explicitDetails' => null, 'types' => ['string'], 'name' => 'nullableStringList', 'nullable' => true, 'isArray' => true],
                ],
            ];

            yield "$key-array<string>|null" => [
                $typeResolver,
                new \ReflectionProperty(DocblockAndTypehintTypes::class, 'nullableStringListUnion'),
                [
                    'reflection' => ['explicitType' => 'mixed', 'explicitDetails' => null, 'types' => ['mixed'], 'name' => 'nullableStringListUnion', 'nullable' => true, 'isArray' => true],
                    'docblock' => ['explicitType' => 'string', 'explicitDetails' => null, 'types' => ['string'], 'name' => 'nullableStringListUnion', 'nullable' => true, 'isArray' => true],
                ],
            ];

            yield "$key-DocblockAndTypehintTypes" => [
                $typeResolver,
                new \ReflectionProperty(DocblockAndTypehintTypes::class, 'class'),
                [
                    'reflection' => ['explicitType' => DocblockAndTypehintTypes::class, 'explicitDetails' => null, 'types' => [DocblockAndTypehintTypes::class], 'name' => 'class', 'nullable' => false, 'isArray' => false],
                    'docblock' => ['explicitType' => DocblockAndTypehintTypes::class, 'explicitDetails' => null, 'types' => [DocblockAndTypehintTypes::class], 'name' => 'class', 'nullable' => false, 'isArray' => false],
                ],
            ];

            yield "$key-?DocblockAndTypehintTypes" => [
                $typeResolver,
                new \ReflectionProperty(DocblockAndTypehintTypes::class, 'nullableClass'),
                [
                    'reflection' => ['explicitType' => DocblockAndTypehintTypes::class, 'explicitDetails' => null, 'types' => [DocblockAndTypehintTypes::class], 'name' => 'nullableClass', 'nullable' => true, 'isArray' => false],
                    'docblock' => ['explicitType' => DocblockAndTypehintTypes::class, 'explicitDetails' => null, 'types' => [DocblockAndTypehintTypes::class], 'name' => 'nullableClass', 'nullable' => true, 'isArray' => false],
                ],
            ];

            yield "$key-\\DateTime" => [
                $typeResolver,
                new \ReflectionProperty(DocblockAndTypehintTypes::class, 'namespacedGlobalClass'),
                [
                    'reflection' => ['explicitType' => \DateTime::class, 'explicitDetails' => null, 'types' => [\DateTime::class], 'name' => 'namespacedGlobalClass', 'nullable' => false, 'isArray' => false],
                    'docblock' => ['explicitType' => \DateTime::class, 'explicitDetails' => null, 'types' => [\DateTime::class], 'name' => 'namespacedGlobalClass', 'nullable' => false, 'isArray' => false],
                ],
            ];

            yield "$key-\\DateTime|null" => [
                $typeResolver,
                new \ReflectionProperty(DocblockAndTypehintTypes::class, 'nullableNamespacedGlobalClass'),
                [
                    'reflection' => ['explicitType' => \DateTime::class, 'explicitDetails' => null, 'types' => [\DateTime::class], 'name' => 'nullableNamespacedGlobalClass', 'nullable' => true, 'isArray' => false],
                    'docblock' => ['explicitType' => \DateTime::class, 'explicitDetails' => null, 'types' => [\DateTime::class], 'name' => 'nullableNamespacedGlobalClass', 'nullable' => true, 'isArray' => false],
                ],
            ];

            yield "$key-null|\\DateTime" => [
                $typeResolver,
                new \ReflectionProperty(DocblockAndTypehintTypes::class, 'alsoNullableNamespacedGlobalClass'),
                [
                    'reflection' => ['explicitType' => \DateTime::class, 'explicitDetails' => null, 'types' => [\DateTime::class], 'name' => 'alsoNullableNamespacedGlobalClass', 'nullable' => true, 'isArray' => false],
                    'docblock' => ['explicitType' => \DateTime::class, 'explicitDetails' => null, 'types' => [\DateTime::class], 'name' => 'alsoNullableNamespacedGlobalClass', 'nullable' => true, 'isArray' => false],
                ],
            ];

            yield "$key-int<min,10>" => [
                $typeResolver,
                new \ReflectionProperty(DocblockAndTypehintTypes::class, 'intRange'),
                [
                    'reflection' => ['explicitType' => 'int', 'explicitDetails' => null, 'types' => ['int'], 'name' => 'intRange', 'nullable' => false, 'isArray' => false],
                    'docblock' => ['explicitType' => 'int', 'explicitDetails' => ['min' => \PHP_INT_MIN, 'max' => 10], 'types' => ['int'], 'name' => 'intRange', 'nullable' => false, 'isArray' => false],
                ],
            ];

            yield "$key-positive-int" => [
                $typeResolver,
                new \ReflectionProperty(DocblockAndTypehintTypes::class, 'positiveInt'),
                [
                    'reflection' => ['explicitType' => 'int', 'explicitDetails' => null, 'types' => ['int'], 'name' => 'positiveInt', 'nullable' => false, 'isArray' => false],
                    'docblock' => ['explicitType' => 'positive-int', 'explicitDetails' => ['min' => 1, 'max' => \PHP_INT_MAX], 'types' => ['int'], 'name' => 'positiveInt', 'nullable' => false, 'isArray' => false],
                ],
            ];

            yield "$key-non-zero-int" => [
                $typeResolver,
                new \ReflectionProperty(DocblockAndTypehintTypes::class, 'nonZeroInt'),
                [
                    'reflection' => ['explicitType' => 'int', 'explicitDetails' => null, 'types' => ['int'], 'name' => 'nonZeroInt', 'nullable' => false, 'isArray' => false],
                    'docblock' => ['explicitType' => 'non-zero-int', 'explicitDetails' => [['min' => \PHP_INT_MIN, 'max' => -1], ['min' => 1, 'max' => \PHP_INT_MAX]], 'types' => ['int'], 'name' => 'nonZeroInt', 'nullable' => false, 'isArray' => false],
                ],
            ];

            yield "$key-array-shape" => [
                $typeResolver,
                new \ReflectionProperty(DocblockAndTypehintTypes::class, 'arrayShape'),
                [
                    'reflection' => ['explicitType' => 'mixed', 'explicitDetails' => null, 'types' => ['mixed'], 'name' => 'arrayShape', 'nullable' => false, 'isArray' => true],
                    'docblock' => ['explicitType' => 'bool', 'explicitDetails' => null, 'types' => ['bool'], 'name' => 'arrayShape', 'nullable' => false, 'isArray' => true],
                ],
            ];

            yield "$key-union-type" => [
                $typeResolver,
                new \ReflectionProperty(DocblockAndTypehintTypes::class, 'unionType'),
                [
                    'reflection' => ['explicitType' => 'int', 'explicitDetails' => null, 'types' => ['int', 'string'], 'name' => 'unionType', 'nullable' => false, 'isArray' => false],
                    'docblock' => ['explicitType' => 'int', 'explicitDetails' => null, 'types' => ['int', 'string'], 'name' => 'unionType', 'nullable' => false, 'isArray' => false],
                ],
            ];

            yield "$key-promoted-string" => [
                $typeResolver,
                (new \ReflectionClass(DocblockAndTypehintTypes::class))->getConstructor()->getParameters()[1],
                [
                    'reflection' => ['explicitType' => 'string', 'explicitDetails' => null, 'types' => ['string'], 'name' => 'promotedString', 'nullable' => false, 'isArray' => false],
                    'docblock' => ['explicitType' => 'string', 'explicitDetails' => null, 'types' => ['string'], 'name' => 'promotedString', 'nullable' => false, 'isArray' => false],
                ],
            ];

            yield "$key-return-string" => [
                $typeResolver,
                (new \ReflectionClass(DocblockAndTypehintTypes::class))->getMethod('getString'),
                [
                    'reflection' => ['explicitType' => 'string', 'explicitDetails' => null, 'types' => ['string'], 'name' => 'getString', 'nullable' => false, 'isArray' => false],
                    'docblock' => ['explicitType' => 'string', 'explicitDetails' => null, 'types' => ['string'], 'name' => 'getString', 'nullable' => false, 'isArray' => false],
                ],
            ];
        }
    }

    /**
     * @dataProvider resolverPropertyCases
     */
    public function testGetReflectionTypeDetails(TypeResolverInterface $typeResolver, \Reflector $reflector, array $expected): void
    {
        $this->assertEquals((object) $expected['reflection'], $typeResolver->getReflectionTypeDetails($reflector));
    }

    /**
     * @dataProvider resolverPropertyCases
     */
    public function testGetDocblockTypeDetails(TypeResolverInterface $typeResolver, \Reflector $reflector, array $expected): void
    {
        $actual = $typeResolver->getDocblockTypeDetails($reflector);
        $this->assertEquals((object) $expected['docblock'], $actual, json_encode(['expected' => (object) $expected['docblock'], 'actual' => $actual]));
    }
}
