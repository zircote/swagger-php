<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Augmenter;

use OpenApi\Augmenter;
use OpenApi\Spec as OA;
use OpenApi\Tests\Concerns\AssemblesSpecification;
use OpenApi\Tests\Fixtures;
use PHPUnit\Framework\TestCase;

final class ShortcutsTest extends TestCase
{
    use AssemblesSpecification;

    public function testMediaTypeJson(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\JsonController::class);

        (new Augmenter\Shortcuts())($spec);

        /** @var OA\MediaType\Json $json */
        $json = $spec->operations[0]->requestBody->content[0];
        $this->assertSame('application/json', $json->mediaType);
        $this->assertInstanceOf(OA\Schema::class, $json->schema);
        $this->assertSame('string', $json->schema->type);
        $this->assertNull($json->ref);
        $this->assertNull($json->type);
        $this->assertNull($json->items);
        $this->assertNull($json->properties);
        $this->assertNull($json->required);
    }

    public function testItems(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\ItemsSchema::class);

        (new Augmenter\Shortcuts())($spec);

        $property = $spec->schemas[0]->properties[0];
        $this->assertInstanceOf(OA\Property::class, $property);
        $this->assertInstanceOf(OA\Schema\Items::class, $property->schema);
        $this->assertSame('array', $property->schema->type);
        $this->assertInstanceOf(OA\Schema::class, $property->schema->items);
    }

    public function testRecursiveItems(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\ItemsSchema::class);

        (new Augmenter\Shortcuts())($spec);

        $property = $spec->schemas[0]->properties[1];
        $this->assertInstanceOf(OA\Property::class, $property);
        $this->assertInstanceOf(OA\Schema\Items::class, $property->schema);
        $this->assertSame('array', $property->schema->type);
        $this->assertInstanceOf(OA\Schema\Items::class, $property->schema->items);
        $this->assertSame('array', $property->schema->items->type);
        $this->assertInstanceOf(OA\Schema::class, $property->schema->items->items);
        $this->assertSame('string', $property->schema->items->items->type);
    }
}
