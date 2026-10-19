<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Spec;

use OpenApi\OpenApiException;
use OpenApi\Spec as OA;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class PropertiesInputTest extends TestCase
{
    public function testAKeyNamesASchemaTypeOrFormat(): void
    {
        $schema = new OA\Schema(properties: [
            'name' => new OA\Schema(type: 'string'),
            'age' => OA\Type::Integer,
            'id' => OA\Format::Uuid,
            new OA\Property(property: 'tag'),
        ]);

        $this->assertSame(['name', 'age', 'id', 'tag'], array_map(static fn (OA\Property $property): ?string => $property->property, $schema->properties ?? []));
        $this->assertSame('uuid', $schema->properties[2]->schema?->format);
    }

    /**
     * @return iterable<string, array{\Closure(): OA\Schema, string}>
     */
    public static function unnamedProvider(): iterable
    {
        yield 'schema' => [static fn (): OA\Schema => new OA\Schema(properties: [new OA\Schema(type: 'string')]), OA\Schema::class];
        yield 'type' => [static fn (): OA\Schema => new OA\Schema(properties: [OA\Type::String]), OA\Type::class . '::String'];
        yield 'format' => [static fn (): OA\Schema => new OA\Schema(properties: [OA\Format::Uuid]), OA\Format::class . '::Uuid'];
    }

    /**
     * Without a key it has no name, and used to vanish from the document without a warning.
     *
     * @param \Closure(): OA\Schema $create
     */
    #[DataProvider('unnamedProvider')]
    public function testOneWithoutAKeyIsAnError(\Closure $create, string $label): void
    {
        $this->expectException(OpenApiException::class);
        $this->expectExceptionMessage("properties: {$label} needs a string key that names the property");

        $create();
    }
}
