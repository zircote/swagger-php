<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Type\Generics;

use OpenApi\Tests\Fixtures\Type\Number;
use OpenApi\Tests\Fixtures\Type\StampedBox;
use OpenApi\Tests\Fixtures\Type\Template;
use OpenApi\Type\Generics\TemplateReader;
use OpenApi\Type\Generics\TypeArguments;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\TypeInfo\Type;

final class TemplateReaderTest extends TestCase
{
    public function testReadsTheTemplatesAClassDeclaresInOrder(): void
    {
        $reader = new TemplateReader();

        $this->assertSame(['T', 'B'], $reader->templateNames(Template::class));
        $this->assertSame([], $reader->templateNames(Number::class));
        $this->assertSame([], $reader->templateNames('App\Missing'));
    }

    public function testAClassTakesOneArgumentForEachTemplate(): void
    {
        $reader = new TemplateReader();

        $this->assertTrue($reader->accepts(Template::class, new TypeArguments(Type::int(), Type::string())));
        $this->assertFalse($reader->accepts(Template::class, new TypeArguments(Type::int())));
        $this->assertFalse($reader->accepts(Number::class, new TypeArguments(Type::int())));
    }

    public function testATemplateStandsForTheArgumentAtItsPosition(): void
    {
        $bindings = (new TemplateReader())->bindings(
            new \ReflectionProperty(Template::class, 'results'),
            Template::class,
            new TypeArguments(Type::int(), Type::string()),
        );

        $this->assertEquals(['T' => Type::int(), 'B' => Type::string()], $bindings);
    }

    /**
     * @return iterable<string, array{\Reflector, class-string, TypeArguments}>
     */
    public static function unmatchedCases(): iterable
    {
        yield 'fewer arguments than templates' => [new \ReflectionProperty(Template::class, 'results'), Template::class, new TypeArguments(Type::int())];
        yield 'more arguments than templates' => [new \ReflectionProperty(StampedBox::class, 'own'), StampedBox::class, new TypeArguments(Type::int(), Type::int())];
        yield 'a class without templates' => [new \ReflectionClass(Number::class), Number::class, new TypeArguments(Type::int())];
        yield 'a parameter of a function' => [new \ReflectionParameter('strlen', 'string'), StampedBox::class, new TypeArguments(Type::int())];
    }

    /**
     * @param class-string $class
     */
    #[DataProvider('unmatchedCases')]
    public function testOneArgumentForEachTemplateOrNoneIsBound(\Reflector $member, string $class, TypeArguments $typeArguments): void
    {
        $this->assertSame([], (new TemplateReader())->bindings($member, $class, $typeArguments));
    }

    /**
     * @return iterable<string, array{\Reflector}>
     */
    public static function ownTemplateCases(): iterable
    {
        yield 'a method' => [new \ReflectionMethod(Template::class, 'same')];
        yield 'a parameter of the method' => [new \ReflectionParameter([Template::class, 'same'], 'value')];
    }

    #[DataProvider('ownTemplateCases')]
    public function testAMethodsOwnTemplateHidesTheClasss(\Reflector $member): void
    {
        $bindings = (new TemplateReader())->bindings($member, Template::class, new TypeArguments(Type::int(), Type::string()));

        $this->assertEquals(['B' => Type::string()], $bindings);
    }

    /**
     * @return iterable<string, array{string, bool}>
     */
    public static function memberCases(): iterable
    {
        yield 'its own' => ['own', true];
        yield 'inherited' => ['value', false];
        yield 'taken from a trait' => ['stamp', false];
    }

    #[DataProvider('memberCases')]
    public function testOnlyAMemberTheClassDeclaresItselfIsBound(string $property, bool $bound): void
    {
        $bindings = (new TemplateReader())->bindings(
            new \ReflectionProperty(StampedBox::class, $property),
            StampedBox::class,
            new TypeArguments(Type::int()),
        );

        $this->assertEquals($bound ? ['T' => Type::int()] : null, $bindings);
    }
}
