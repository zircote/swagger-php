<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Type;

use OpenApi\Type\SchemaType;
use OpenApi\Type\TypeArguments;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\TypeInfo\Type;

final class SchemaTypeTest extends TestCase
{
    /**
     * @return iterable<string, array{SchemaType, bool}>
     */
    public static function typeArgumentCases(): iterable
    {
        $generic = new SchemaType(type: 'App\Envelope', typeArguments: new TypeArguments(Type::int()));
        $plain = new SchemaType(type: 'App\Envelope');

        yield 'plain class' => [$plain, false];
        yield 'scalar' => [new SchemaType(type: 'integer'), false];
        yield 'generic class' => [$generic, true];
        yield 'items' => [new SchemaType(type: 'array', items: $generic), true];
        yield 'map values' => [new SchemaType(type: 'object', additionalProperties: $generic), true];
        yield 'union member' => [new SchemaType(oneOf: [$plain, $generic]), true];
        yield 'intersection member' => [new SchemaType(allOf: [$generic]), true];
        yield 'shape property' => [new SchemaType(type: 'object', properties: ['data' => $generic]), true];
        yield 'nested plain' => [new SchemaType(type: 'array', items: new SchemaType(oneOf: [$plain])), false];
    }

    #[DataProvider('typeArgumentCases')]
    public function testFindsTypeArgumentsAnywhereInTheType(SchemaType $type, bool $expected): void
    {
        $this->assertSame($expected, $type->hasTypeArguments());
    }
}
