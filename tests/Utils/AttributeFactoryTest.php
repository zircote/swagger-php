<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Utils;

use OpenApi\AttributeInterface;
use OpenApi\OpenApiException;
use OpenApi\Spec as OA;
use OpenApi\Tests\Fixtures\Assembler\AmbiguousMerge;
use OpenApi\Tests\Fixtures\Assembler\SimpleController;
use OpenApi\Tests\Fixtures\Assembler\SimpleProduct;
use OpenApi\Utils\AttributeFactory;
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
            [SimpleController::class, 'getProduct'],
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
}
