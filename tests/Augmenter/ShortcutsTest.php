<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Augmenter;

use OpenApi\Augmenter;
use OpenApi\Spec as OA;
use OpenApi\Tests\Concerns\AssemblesSpecification;
use OpenApi\Tests\Fixtures;
use PHPUnit\Framework\Attributes\DataProvider;
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

    public static function nestedItemsProvider(): \Generator
    {
        yield 'oneOf' => [static fn (OA\Schema $schema): ?OA\Schema => $schema->oneOf[0] ?? null];
        yield 'allOf' => [static fn (OA\Schema $schema): ?OA\Schema => $schema->allOf[0] ?? null];
        yield 'not' => [static fn (OA\Schema $schema): ?OA\Schema => $schema->not];
        yield 'additionalProperties' => [static fn (OA\Schema $schema): ?OA\Schema => $schema->additionalProperties instanceof OA\Schema ? $schema->additionalProperties : null];
    }

    /**
     * @param \Closure(OA\Schema): ?OA\Schema $nested
     */
    #[DataProvider('nestedItemsProvider')]
    public function testNestedItems(\Closure $nested): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\ItemsNestedSchema::class);

        (new Augmenter\Shortcuts())($spec);

        $name = $this->dataName();
        $property = array_values(array_filter($spec->schemas[0]->properties ?? [], static fn (OA\Property $property): bool => $property->getReflector() instanceof \ReflectionProperty && $property->getReflector()->getName() === $name))[0] ?? null;
        $this->assertInstanceOf(OA\Property::class, $property);
        $this->assertInstanceOf(OA\Schema::class, $property->schema);

        $items = $nested($property->schema);
        $this->assertInstanceOf(OA\Schema\Items::class, $items);
        $this->assertSame('array', $items->type);
        $this->assertInstanceOf(OA\Schema::class, $items->items);
        $this->assertSame('string', $items->items->type);
    }

    /**
     * In the `items` of a plain schema, an `Items` is the item schema rather than an array of them.
     */
    public function testItemsInTheItemsOfASchemaIsTheItemSchema(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\ItemsNestedSchema::class);

        (new Augmenter\Shortcuts())($spec);

        $property = array_values(array_filter($spec->schemas[0]->properties ?? [], static fn (OA\Property $property): bool => $property->getReflector() instanceof \ReflectionProperty && $property->getReflector()->getName() === 'items'))[0] ?? null;
        $this->assertInstanceOf(OA\Property::class, $property);
        $this->assertSame('array', $property->schema?->type);
        $this->assertInstanceOf(OA\Schema\Items::class, $property->schema->items);
        $this->assertSame('string', $property->schema->items->type);
        $this->assertNull($property->schema->items->items);
    }

    public function testItemsComponent(): void
    {
        $spec = $this->assemble(Fixtures\Augmenter\ItemsComponent::class);

        (new Augmenter\Shortcuts())($spec);

        $schema = $spec->schemas[0];
        $this->assertSame('TagList', $schema->component);
        $this->assertSame('array', $schema->type);
        $this->assertInstanceOf(OA\Schema::class, $schema->items);
        $this->assertSame('string', $schema->items->type);
        $this->assertNull($schema->items->component);
    }

    /**
     * `fan` is a constructor argument of `Schema\Items` without a field of its own, and stays with
     * the array rather than moving into its items.
     */
    public function testItemsKeepTheirFan(): void
    {
        $spec = $this->assemble(Fixtures\Fan\DeepController::class);

        (new Augmenter\Shortcuts())($spec);

        foreach ($spec->operations as $operation) {
            $schema = $operation->responses[0]->content[0]->schema;
            $this->assertInstanceOf(OA\Schema\Items::class, $schema);
            $this->assertSame('array', $schema->type);
            $this->assertSame(1, $schema->minItems);
            $this->assertSame(OA\Fan::Single, $schema->getFan());
            $this->assertInstanceOf(OA\Schema::class, $schema->items);
            $this->assertSame('string', $schema->items->type);
            $this->assertNull($schema->items->getFan());
        }
    }
}
