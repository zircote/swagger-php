<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tools\Tests\Docs;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * The reference renders docblock types verbatim, so these two are the difference between a
 * published type a reader can act on and one that is silently wrong.
 */
class DocGeneratorTest extends TestCase
{
    protected TestDocGenerator $generator;

    protected function setUp(): void
    {
        $this->generator = new TestDocGenerator(__DIR__);
    }

    /**
     * @param list<string> $expected
     */
    #[DataProvider('unionCases')]
    public function testSplitUnion(string $type, array $expected): void
    {
        $this->assertSame($expected, $this->generator->split($type));
    }

    /**
     * @return iterable<string, array{string, list<string>}>
     */
    public static function unionCases(): iterable
    {
        yield 'plain' => ['string|int', ['string', 'int']];
        yield 'generic is one part' => ['array<string|int>|null', ['array<string|int>', 'null']];
        yield 'nested generic' => ['list<array<string|int>>|string', ['list<array<string|int>>', 'string']];
        yield 'shape' => ['array{a: string|int}|null', ['array{a: string|int}', 'null']];
        yield 'callable' => ['callable(string|int): void|null', ['callable(string|int): void', 'null']];
        yield 'single' => ['string', ['string']];
    }

    /**
     * A duplicate inside a generic used to collide with the same name outside it, and
     * `array_unique()` dropped the outer one — `JsonContent` disappeared from the published
     * type for `Parameter::$content` that way.
     */
    public function testSplitUnionKeepsARepeatedNameOutsideItsGeneric(): void
    {
        $this->assertSame(
            ['array<MediaType|JsonContent>', 'MediaType', 'JsonContent', 'null'],
            $this->generator->split('array<MediaType|JsonContent>|MediaType|JsonContent|null')
        );
    }

    public function testParseDocblockCapturesAliasesAndKeepsThemOutOfTheDescription(): void
    {
        $parsed = $this->generator->parseDocblock(<<<'DOC'
            /**
             * A description.
             *
             * @phpstan-type EnumValues list<string|int>|null
             * @phpstan-import-type Other from SomeClass
             */
            DOC);

        $this->assertSame(['EnumValues' => 'list<string|int>|null'], $parsed['aliases']);
        $this->assertSame(['Other' => ['name' => 'Other', 'from' => 'SomeClass']], $parsed['imports']);
        $this->assertStringNotContainsString('@phpstan', $parsed['content']);
        $this->assertSame('A description.', $parsed['content']);
    }

    public function testExpandsALocalAlias(): void
    {
        $doc = ['aliases' => ['EnumValues' => 'list<string>|null'], 'imports' => []];

        $this->assertSame(
            'list<string>|null',
            $this->generator->expand('EnumValues', $doc, new \ReflectionClass(self::class))
        );
    }

    public function testFollowsAnImportToTheDeclaringClass(): void
    {
        $doc = ['aliases' => [], 'imports' => ['EnumValues' => ['name' => 'EnumValues', 'from' => 'TypeAliasFixture']]];

        $this->assertSame(
            'list<string|int|float|bool|\UnitEnum|null>|class-string|null',
            $this->generator->expand('EnumValues', $doc, new \ReflectionClass(self::class))
        );
    }

    public function testLeavesAnUnknownNameAlone(): void
    {
        $doc = ['aliases' => ['EnumValues' => 'list<string>'], 'imports' => []];

        $this->assertSame(
            'SomeClass|null',
            $this->generator->expand('SomeClass|null', $doc, new \ReflectionClass(self::class))
        );
    }

    public function testDoesNotSubstituteInsideALongerName(): void
    {
        $doc = ['aliases' => ['Enum' => 'string', 'EnumValues' => 'list<string>'], 'imports' => []];

        $this->assertSame(
            'list<string>',
            $this->generator->expand('EnumValues', $doc, new \ReflectionClass(self::class))
        );
    }
}
