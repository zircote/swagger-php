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

    public function testOrphanAttributeThrows(): void
    {
        $this->expectException(OpenApiException::class);
        $this->expectExceptionMessageMatches('/Orphan attribute/');

        $assembler = new Assembler();
        $assembler->collect(new \ReflectionClass(Fixtures\Assembler\OrphanProperty::class));
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
}
