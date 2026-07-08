<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Utils;

use OpenApi\Spec;
use OpenApi\Tests\OpenApiTestCase;
use OpenApi\Utils\DocBlockParser;
use PHPUnit\Framework\Attributes\DataProvider;

final class DocBlockParserTest extends OpenApiTestCase
{
    private DocBlockParser $parser;

    protected function setUp(): void
    {
        $this->parser = new DocBlockParser();
    }

    public static function paramArrayItemTypesCases(): iterable
    {
        yield 'simple list' => [
            '/** @param list<Server> $servers */',
            'servers',
            ['Server'],
        ];

        yield 'nullable list' => [
            '/** @param list<Flow>|null $flows */',
            'flows',
            ['Flow'],
        ];

        yield 'union item types' => [
            '/** @param list<Foo|Bar> $items */',
            'items',
            ['Foo', 'Bar'],
        ];

        yield 'array generic' => [
            '/** @param array<Schema> $schemas */',
            'schemas',
            ['Schema'],
        ];

        yield 'array with key type' => [
            '/** @param array<string, Response> $responses */',
            'responses',
            ['Response'],
        ];

        yield 'fqcn item type' => [
            '/** @param list<\OpenApi\Spec\Tag> $tags */',
            'tags',
            ['\\' . Spec\Tag::class],
        ];

        yield 'nullable via question mark' => [
            '/** @param ?list<Header> $headers */',
            'headers',
            ['Header'],
        ];

        yield 'non-matching param name' => [
            '/** @param list<Server> $servers */',
            'other',
            null,
        ];

        yield 'non-generic type' => [
            '/** @param string $name */',
            'name',
            null,
        ];

        yield 'empty docblock' => [
            '',
            'foo',
            null,
        ];
    }

    #[DataProvider('paramArrayItemTypesCases')]
    public function testGetParamArrayItemTypes(string $docblock, string $paramName, ?array $expected): void
    {
        $this->assertSame($expected, $this->parser->getParamArrayItemTypes($docblock, $paramName));
    }
}
