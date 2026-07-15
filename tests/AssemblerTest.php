<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Assembler;
use OpenApi\OpenApiException;
use PHPUnit\Framework\TestCase;

final class AssemblerTest extends TestCase
{
    public function testHierarchyPropertyAbsorbedBySchema(): void
    {
        $assembler = new Assembler();
        $assembler->collect(new \ReflectionClass(Fixtures\Assembler\SimpleProduct::class));

        $spec = $assembler->getSpecification();
        $this->assertCount(1, $spec->schemas);
        $this->assertEquals('SimpleProduct', $spec->schemas[0]->schema);
        $this->assertNotNull($spec->schemas[0]->properties);
        $this->assertCount(2, $spec->schemas[0]->properties);
    }

    public function testHierarchyPromotedPropertyAbsorbedBySchema(): void
    {
        $assembler = new Assembler();
        $assembler->collect(new \ReflectionClass(Fixtures\Assembler\PromotedProduct::class));

        $spec = $assembler->getSpecification();
        $this->assertCount(1, $spec->schemas);
        $this->assertNotNull($spec->schemas[0]->properties);
        $this->assertCount(2, $spec->schemas[0]->properties);

        $names = array_map(fn ($p) => $p->property, $spec->schemas[0]->properties);
        $this->assertContains('quantity', $names);
        $this->assertContains('brand', $names);
    }

    public function testHierarchyMethodParametersAbsorbedByOperation(): void
    {
        $assembler = new Assembler();
        $assembler->collect(new \ReflectionClass(Fixtures\Assembler\SimpleController::class));

        $spec = $assembler->getSpecification();
        $this->assertCount(1, $spec->operations);
        $this->assertEquals('/products/{product_id}', $spec->operations[0]->path);
        $this->assertNotNull($spec->operations[0]->parameters);
        $this->assertCount(1, $spec->operations[0]->parameters);
        $this->assertEquals('product_id', $spec->operations[0]->parameters[0]->name);
    }

    public function testClassWithoutRootAttributeSkipped(): void
    {
        $assembler = new Assembler();
        $assembler->collect(new \ReflectionClass(Fixtures\Assembler\OrphanProperty::class));

        $spec = $assembler->getSpecification();
        $this->assertCount(0, $spec->schemas);
    }

    public function testClassConstantAbsorbedBySchema(): void
    {
        $assembler = new Assembler();
        $assembler->collect(new \ReflectionClass(Fixtures\Assembler\WithConstant::class));

        $spec = $assembler->getSpecification();
        $this->assertCount(1, $spec->schemas);
        $this->assertNotNull($spec->schemas[0]->properties);
        $this->assertCount(1, $spec->schemas[0]->properties);
        $this->assertEquals('kind', $spec->schemas[0]->properties[0]->property);
    }

    public function testParameterOnClassWithoutPathItemThrows(): void
    {
        $this->expectException(OpenApiException::class);
        $this->expectExceptionMessageMatches('/Non-root attribute.*Parameter.*remains after resolution/');

        $assembler = new Assembler();
        $assembler->collect(new \ReflectionClass(Fixtures\Assembler\OrphanParameter::class));
    }

    public function testSecurityRequirementOnClassWithoutPathItemThrows(): void
    {
        $this->expectException(OpenApiException::class);
        $this->expectExceptionMessageMatches('/Non-root attribute.*Requirement.*remains after resolution/');

        $assembler = new Assembler();
        $assembler->collect(new \ReflectionClass(Fixtures\Assembler\OrphanSecurity::class));
    }

    public function testAttachablesAreRootsAndDontMergeByDefault(): void
    {
        $assembler = new Assembler();
        $assembler->collect(new \ReflectionClass(Fixtures\Assembler\WithAttachables::class));

        $spec = $assembler->getSpecification();
        $this->assertCount(2, $spec->attachables);
        $this->assertNotNull($spec->schemas[0]->attachables);
        $this->assertCount(1, $spec->schemas[0]->attachables);
        $this->assertCount(2, $spec->schemas[0]->properties[0]->attachables);
    }

    public function testInvalidSlotValidation(): void
    {
        $this->expectException(OpenApiException::class);
        $this->expectExceptionMessageMatches('/Invalid slot: "badSlot"/');

        $assembler = new Assembler();
        $assembler->collect(new \ReflectionClass(Fixtures\Assembler\WithInvalidAttachables::class));
    }
}
