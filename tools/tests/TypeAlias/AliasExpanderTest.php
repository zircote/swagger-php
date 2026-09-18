<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tools\Tests\TypeAlias;

use OpenApi\Tools\TypeAlias\AliasExpander;
use PHPUnit\Framework\TestCase;

class AliasExpanderTest extends TestCase
{
    public function testParsesADeclarationAndAnImport(): void
    {
        $parsed = AliasExpander::parse(<<<'DOC'
            /**
             * @phpstan-type EnumValue string|int
             * @phpstan-import-type Other from SomeClass
             * @phpstan-import-type Third from \Some\Ns\Cls as Local
             */
            DOC);

        $this->assertSame(['EnumValue' => 'string|int'], $parsed['aliases']);
        $this->assertSame([
            'Other' => ['name' => 'Other', 'from' => 'SomeClass'],
            'Local' => ['name' => 'Third', 'from' => '\Some\Ns\Cls'],
        ], $parsed['imports']);
    }

    public function testParsesNothingFromAnEmptyDocblock(): void
    {
        $this->assertSame(['aliases' => [], 'imports' => []], AliasExpander::parse(false));
    }

    public function testExpandsALocalAlias(): void
    {
        $this->assertSame(
            'list<string|int|float|bool|\UnitEnum|null>',
            AliasExpander::expand('list<EnumValue|null>', new \ReflectionClass(DeclaringFixture::class))
        );
    }

    public function testFollowsAnImportToItsDeclaringClass(): void
    {
        $this->assertSame(
            'list<string|int|float|bool|\UnitEnum|null>|class-string|null',
            AliasExpander::expand('list<EnumValue|null>|class-string|null', new \ReflectionClass(ImportingFixture::class))
        );
    }

    public function testFindsAnAliasImportedByATraitTheClassUses(): void
    {
        // ReflectionProperty::getDeclaringClass() names the using class, not the trait, so an
        // alias reachable only through the trait has to be found by walking the scopes.
        $this->assertSame(
            'list<string|int|float|bool|\UnitEnum>',
            AliasExpander::expand('list<EnumValue>', new \ReflectionClass(InheritingFixture::class))
        );
    }

    public function testLeavesAnUnknownNameAlone(): void
    {
        $this->assertSame(
            'SomeClass|null',
            AliasExpander::expand('SomeClass|null', new \ReflectionClass(DeclaringFixture::class))
        );
    }

    public function testPrefersTheLongerAliasName(): void
    {
        $this->assertSame(
            'list<string>',
            AliasExpander::expand('EnumValues', new \ReflectionClass(OverlappingFixture::class))
        );
    }

    public function testReturnsTheTypeUntouchedWhenNothingIsDeclared(): void
    {
        $this->assertSame(
            'EnumValue|null',
            AliasExpander::expand('EnumValue|null', new \ReflectionClass(NoAliasFixture::class))
        );
    }
}
