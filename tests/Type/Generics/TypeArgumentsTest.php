<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Type\Generics;

use OpenApi\Tests\Fixtures\Type\Number;
use OpenApi\Tests\Fixtures\Type\Template;
use OpenApi\Tests\Fixtures\Type\TypeArgumentUse;
use OpenApi\Type\Generics\TypeArguments;
use OpenApi\Type\SchemaType;
use OpenApi\Type\TypeResolver;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\TypeInfo\Type;

final class TypeArgumentsTest extends TestCase
{
    public function testTheTypesKeepTheirOrder(): void
    {
        $arguments = new TypeArguments(Type::string(), Type::int());

        $this->assertEquals([Type::string(), Type::int()], $arguments->types);
        $this->assertSame('string, int', (string) $arguments);
    }

    public function testAClassNameIsWrittenWithoutALeadingBackslash(): void
    {
        $arguments = new TypeArguments(Type::object('\App\Customer'), Type::list(Type::object('\App\Order')));

        $this->assertSame('App\Customer, list<App\Order>', (string) $arguments);
        $this->assertSame((string) $arguments, (string) new TypeArguments(Type::object('App\Customer'), Type::list(Type::object('App\Order'))));
    }

    /**
     * @return iterable<string, array{string, \Closure(SchemaType): ?SchemaType, string}>
     */
    public static function useCases(): iterable
    {
        $itself = static fn (SchemaType $type): SchemaType => $type;

        yield 'a class' => ['single', $itself, Number::class . ', JsonSerializable'];
        yield 'a nullable class' => ['nullable', $itself, 'int<0, 100>, JsonSerializable'];
        yield 'the items of a list' => ['items', static fn (SchemaType $type): ?SchemaType => $type->items, 'list<' . Number::class . '>, JsonSerializable'];
        yield 'the values of a map' => [
            'map',
            static fn (SchemaType $type): ?SchemaType => $type->additionalProperties instanceof SchemaType ? $type->additionalProperties : null,
            'int, JsonSerializable',
        ];
        yield 'a member of a union' => [
            'union',
            static fn (SchemaType $type): ?SchemaType => array_values(array_filter($type->oneOf ?? [], static fn (SchemaType $member): bool => $member->type === Template::class))[0] ?? null,
            Number::class . ', JsonSerializable',
        ];
    }

    /**
     * @param \Closure(SchemaType): ?SchemaType $use
     */
    #[DataProvider('useCases')]
    public function testAClassUsedWithTypeArgumentsKeepsThem(string $property, \Closure $use, string $expected): void
    {
        $type = $use($this->resolve($property));

        $this->assertSame(Template::class, $type?->type);
        $this->assertSame($expected, (string) $type->typeArguments);
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function withoutCases(): iterable
    {
        yield 'a class used without them' => ['plain'];
        yield 'a class mapped to a format' => ['date'];
        yield 'a class that is a collection' => ['collection'];
    }

    #[DataProvider('withoutCases')]
    public function testOnlyAReferencedClassKeepsTypeArguments(string $property): void
    {
        $this->assertNull($this->resolve($property)->typeArguments);
    }

    private function resolve(string $property): SchemaType
    {
        $type = (new TypeResolver())->resolve(new \ReflectionProperty(TypeArgumentUse::class, $property));
        $this->assertInstanceOf(SchemaType::class, $type);

        return $type;
    }
}
