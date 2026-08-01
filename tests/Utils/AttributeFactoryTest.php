<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Utils;

use OpenApi\Assembler;
use OpenApi\Assembler\AbstractAttributeTranslator;
use OpenApi\AttributeInterface;
use OpenApi\AttributeTranslatorInterface;
use OpenApi\OpenApiException;
use OpenApi\Spec as OA;
use OpenApi\Tests\Fixtures\Assembler\AmbiguousMerge;
use OpenApi\Tests\Fixtures\Assembler\Attachable\RequestPayload;
use OpenApi\Tests\Fixtures\Assembler\SimpleController;
use OpenApi\Tests\Fixtures\Assembler\SimpleProduct;
use OpenApi\Utils\AttributeFactory;
use OpenApi\Utils\TypedList;
use PHPUnit\Framework\TestCase;

final class AttributeFactoryTest extends TestCase
{
    public function testFromReflectorProperty(): void
    {
        $factory = new AttributeFactory();
        $result = $factory->fromReflector(new \ReflectionProperty(SimpleProduct::class, 'name'));

        $this->assertCount(1, $result);
        $this->assertInstanceOf(OA\Property::class, $result[0]);
        $this->assertSame('name', $result[0]->property);
        $this->assertInstanceOf(OA\Schema::class, $result[0]->schema);
        $this->assertSame('The name.', $result[0]->schema->description);
    }

    public function testFromReflectorParameter(): void
    {
        $factory = new AttributeFactory();
        $result = $factory->fromReflector(new \ReflectionParameter(
            [SimpleController::class, 'addProduct'],
            'product_id',
        ));

        $this->assertCount(1, $result);
        $this->assertInstanceOf(OA\Parameter::class, $result[0]);
        $this->assertSame('product_id', $result[0]->name);
        $this->assertInstanceOf(OA\Schema::class, $result[0]->schema);
        $this->assertSame('int64', $result[0]->schema->format);
    }

    public function testFromReflectorAmbiguousMergeThrows(): void
    {
        $this->expectException(OpenApiException::class);
        $this->expectExceptionMessageMatches('/Ambiguous merge/');

        $factory = new AttributeFactory();
        $factory->fromReflector(new \ReflectionProperty(AmbiguousMerge::class, 'value'));
    }

    public function testMembersOfCollectsOwnProperties(): void
    {
        $factory = new AttributeFactory();
        $members = $factory->membersOf(new \ReflectionClass(SimpleProduct::class));

        $propertyNames = array_map(
            fn (AttributeInterface $attr): ?string => $attr instanceof OA\Property ? $attr->property : null,
            $members,
        );

        $this->assertContains('name', $propertyNames);
    }

    public function testHasAttributesTrue(): void
    {
        $factory = new AttributeFactory();

        $this->assertTrue($factory->hasAttributes(new \ReflectionClass(SimpleProduct::class)));
    }

    public function testHasAttributesFalse(): void
    {
        $factory = new AttributeFactory();

        $this->assertFalse($factory->hasAttributes(new \ReflectionClass(\stdClass::class)));
    }

    public function testResolveHierarchyAbsorbsChildren(): void
    {
        $factory = new AttributeFactory();

        $outer = $factory->fromReflector(new \ReflectionClass(SimpleProduct::class));
        $inner = $factory->membersOf(new \ReflectionClass(SimpleProduct::class));

        $roots = $factory->resolveHierarchy($outer, $inner);

        $this->assertCount(1, $roots);
        $this->assertInstanceOf(OA\Schema::class, $roots[0]);
        $this->assertNotEmpty($roots[0]->properties);
    }

    public function testAttributeTranslatorModifies(): void
    {
        $factory = (new AttributeFactory())
            ->withTranslators(
                fn (TypedList $translators): TypedList => $translators->add(
                    new class () extends AbstractAttributeTranslator {
                        public function getAttributes(\ReflectionClassConstant|\ReflectionParameter|\ReflectionMethod|\ReflectionClass|\ReflectionProperty $reflector): array
                        {
                            return [];
                        }

                        public function translate(array $attributes, array $created, \ReflectionClassConstant|\ReflectionParameter|\ReflectionMethod|\ReflectionClass|\ReflectionProperty $reflector): array
                        {
                            $property = $attributes[0];
                            if ($property instanceof OA\Property && $property->property === 'name') {
                                $property->property = 'other';
                            }

                            return [...$attributes, ...$created];
                        }
                    }
                )
            );

        $inner = $factory->membersOf(new \ReflectionClass(SimpleProduct::class));

        $this->assertCount(2, $inner);
        $this->assertInstanceOf(OA\Property::class, $inner[0]);
        $this->assertSame('other', $inner[0]->property);
    }

    public function testAttributeTranslatorAdds(): void
    {
        $factory = (new AttributeFactory())
            ->withTranslators(
                fn (TypedList $translators): TypedList => $translators->add(
                    new class () extends AbstractAttributeTranslator {
                        public function getAttributes(\ReflectionClassConstant|\ReflectionParameter|\ReflectionMethod|\ReflectionClass|\ReflectionProperty $reflector): array
                        {
                            return [];
                        }

                        public function translate(array $attributes, array $created, \ReflectionClassConstant|\ReflectionParameter|\ReflectionMethod|\ReflectionClass|\ReflectionProperty $reflector): array
                        {
                            if ($attributes[0] instanceof OA\Schema && $attributes[0]->schema === 'SimpleProduct') {
                                $property = new OA\Property(
                                    property: 'extra',
                                    schema: new OA\Schema(type: 'bool'),
                                );
                                $attributes[0]->properties[] = $property;
                            }

                            return [...$attributes, ...$created];
                        }
                    }
                )
            );

        $assembler = new Assembler(attributeFactory: $factory);
        $spec = $assembler->collect(new \ReflectionClass(SimpleProduct::class))
            ->getSpecification();

        $this->assertCount(1, $spec->schemas);
        $schema = $spec->schemas[0];
        $this->assertCount(3, $schema->properties);
        $this->assertSame('extra', $schema->properties[0]->property);
    }

    public function testCustomTranslateMerge(): void
    {
        $factory = (new AttributeFactory())
            ->withTranslators(
                fn (TypedList $translators): TypedList => $translators->add(
                    new class () implements AttributeTranslatorInterface {
                        protected OA\Operation|null $operation = null;

                        public function reset(): void
                        {
                            $this->operation = null;
                        }

                        public function getAttributes(\ReflectionClassConstant|\ReflectionParameter|\ReflectionMethod|\ReflectionClass|\ReflectionProperty $reflector): array
                        {
                            if ($reflector instanceof \ReflectionParameter) {
                                return $reflector->getAttributes(
                                    RequestPayload::class,
                                    \ReflectionAttribute::IS_INSTANCEOF,
                                );
                            };

                            return [];
                        }

                        public function translate(array $attributes, array $created, \ReflectionClassConstant|\ReflectionParameter|\ReflectionMethod|\ReflectionClass|\ReflectionProperty $reflector): array
                        {
                            foreach ($attributes as $attribute) {
                                if ($attribute instanceof OA\Operation) {
                                    $this->operation = $attribute;
                                }
                            }

                            foreach ($created as $attribute) {
                                if ($attribute instanceof RequestPayload) {
                                    $requestBody = new OA\RequestBody(
                                        content: new OA\MediaType\Json(ref: $reflector->getName()),
                                    );

                                    if ($this->operation) {
                                        $this->operation->requestBody = $requestBody;
                                    }
                                }
                            }

                            return array_filter([...$attributes, ...$created], fn (object $attribute): bool => !($attribute instanceof RequestPayload));
                        }
                    }
                )
            );

        $assembler = new Assembler(attributeFactory: $factory);
        $spec = $assembler->collect(
            new \ReflectionClass(SimpleController::class),
            new \ReflectionClass(SimpleProduct::class),
        )
            ->getSpecification();

        $this->assertCount(1, $spec->operations);
        $operation = $spec->operations[0];
        $this->assertInstanceOf(OA\RequestBody::class, $operation->requestBody);
    }
}
