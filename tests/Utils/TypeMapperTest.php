<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Utils;

use OpenApi\Tests\OpenApiTestCase;
use OpenApi\Utils\TypeMapper;
use PHPUnit\Framework\Attributes\DataProvider;

final class TypeMapperTest extends OpenApiTestCase
{
    private TypeMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new TypeMapper();
    }

    public static function mapCases(): iterable
    {
        yield 'string' => ['string', ['type' => 'string', 'format' => null]];
        yield 'int' => ['int', ['type' => 'integer', 'format' => null]];
        yield 'integer' => ['integer', ['type' => 'integer', 'format' => null]];
        yield 'bool' => ['bool', ['type' => 'boolean', 'format' => null]];
        yield 'boolean' => ['boolean', ['type' => 'boolean', 'format' => null]];
        yield 'float' => ['float', ['type' => 'number', 'format' => 'float']];
        yield 'double' => ['double', ['type' => 'number', 'format' => 'double']];
        yield 'number' => ['number', ['type' => 'number', 'format' => null]];
        yield 'array' => ['array', ['type' => 'array', 'format' => null]];
        yield 'object' => ['object', ['type' => 'object', 'format' => null]];
        yield 'byte' => ['byte', ['type' => 'string', 'format' => 'byte']];
        yield 'date' => ['date', ['type' => 'string', 'format' => 'date']];
        yield 'datetime' => ['datetime', ['type' => 'string', 'format' => 'date-time']];
        yield '\\datetime' => ['\\datetime', ['type' => 'string', 'format' => 'date-time']];
        yield 'datetimeimmutable' => ['datetimeimmutable', ['type' => 'string', 'format' => 'date-time']];
        yield '\\datetimeimmutable' => ['\\datetimeimmutable', ['type' => 'string', 'format' => 'date-time']];
        yield 'datetimeinterface' => ['datetimeinterface', ['type' => 'string', 'format' => 'date-time']];
        yield '\\datetimeinterface' => ['\\datetimeinterface', ['type' => 'string', 'format' => 'date-time']];
        yield 'mixed' => ['mixed', ['type' => 'mixed', 'format' => null]];
        yield 'long' => ['long', ['type' => 'integer', 'format' => 'long']];
        yield 'case insensitive' => ['String', ['type' => 'string', 'format' => null]];
        yield 'unknown' => ['callable', null];
        yield 'resource' => ['resource', null];
        yield 'iterable' => ['iterable', null];
        yield 'class name' => ['App\\Model\\User', null];
    }

    #[DataProvider('mapCases')]
    public function testMap(string $type, ?array $expected): void
    {
        $this->assertSame($expected, $this->mapper->map($type));
    }

    public static function toSpecTypeCases(): iterable
    {
        yield 'string' => ['string', 'string'];
        yield 'int' => ['int', 'integer'];
        yield 'float' => ['float', 'number'];
        yield 'datetime' => ['datetime', 'string'];
        yield 'unknown passthrough' => ['SomeClass', 'SomeClass'];
    }

    #[DataProvider('toSpecTypeCases')]
    public function testToSpecType(string $type, string $expected): void
    {
        $this->assertSame($expected, $this->mapper->toSpecType($type));
    }

    public function testToSpecTypes(): void
    {
        $this->assertSame(
            ['string', 'integer', 'number'],
            $this->mapper->toSpecTypes(['string', 'int', 'float'])
        );
    }

    public static function hasOpenApiTypeCases(): iterable
    {
        yield 'string' => ['string', true];
        yield 'int' => ['int', true];
        yield 'null' => ['null', true];
        yield 'boolean' => ['boolean', true];
        yield 'mixed' => ['mixed', false];
        yield 'callable' => ['callable', false];
        yield 'resource' => ['resource', false];
        yield 'iterable' => ['iterable', false];
        yield 'void' => ['void', false];
    }

    #[DataProvider('hasOpenApiTypeCases')]
    public function testHasOpenApiType(string $type, bool $expected): void
    {
        $this->assertSame($expected, $this->mapper->hasOpenApiType($type));
    }
}
