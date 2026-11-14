<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Type\Generics;

use OpenApi\Tests\Fixtures\Type\Template;
use OpenApi\Type\Generics\TemplateBinder;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Radebatz\TypeInfoExtras\TypeResolver\StringTypeResolver;
use Symfony\Component\TypeInfo\Exception\InvalidArgumentException;
use Symfony\Component\TypeInfo\Type;
use Symfony\Component\TypeInfo\Type\ArrayShapeType;
use Symfony\Component\TypeInfo\Type\TemplateType;
use Symfony\Component\TypeInfo\TypeContext\TypeContextFactory;
use Symfony\Component\TypeInfo\TypeResolver\StringTypeResolver as SymfonyStringTypeResolver;

final class TemplateBinderTest extends TestCase
{
    /**
     * @return iterable<string, array{string, string}>
     */
    public static function boundCases(): iterable
    {
        yield 'a template' => ['T', 'int'];
        yield 'a nullable template' => ['?T', 'int|null'];
        yield 'a list' => ['list<T>', 'list<int>'];
        yield 'a map' => ['array<string, T>', 'array<string, int>'];
        yield 'an iterable' => ['iterable<T>', 'iterable<int|string, int>'];
        yield 'an array shape' => ['array{id: T, rest?: list<T>}', "array{'id': int, 'rest'?: list<int>}"];
        yield 'a union' => ['T|string', 'int|string'];
        yield 'a nullable union' => ['T|string|null', 'int|null|string'];
        yield 'a union of the same type twice' => ['T|int', 'int'];
        yield 'an intersection' => ['B&\Countable', 'ArrayObject&Countable'];
        yield 'a generic class' => ['Template<T, B>', Template::class . '<int, ArrayObject>'];
        yield 'a generic class in a list' => ['list<Template<T, B>>', 'list<' . Template::class . '<int, ArrayObject>>'];
        yield 'no template' => ['list<string>', 'list<string>'];
    }

    #[DataProvider('boundCases')]
    public function testReplacesEachTemplate(string $type, string $expected): void
    {
        $bound = (new TemplateBinder())->bind($this->type($type), ['T' => Type::int(), 'B' => Type::object(\ArrayObject::class)]);

        $this->assertSame($expected, (string) $bound);
    }

    public function testAnUnsealedArrayShapeKeepsItsExtraTypes(): void
    {
        $probe = new ArrayShapeType(['id' => ['type' => Type::int(), 'optional' => false]], Type::string(), Type::bool());
        if ((string) $probe->getExtraValueType() !== 'bool') {
            $this->markTestSkipped('symfony/type-info before 7.4.6 gives the extra values of an unsealed shape the type of its extra keys.');
        }

        $binder = new TemplateBinder();
        $shape = new ArrayShapeType(['id' => ['type' => Type::int(), 'optional' => false]], Type::string(), new TemplateType('T', Type::mixed()));

        $this->assertTrue($binder->hasTemplate($shape));

        $bound = $binder->bind($shape, ['T' => Type::int()]);

        $this->assertInstanceOf(ArrayShapeType::class, $bound);
        $this->assertSame('string', (string) $bound->getExtraKeyType());
        $this->assertSame('int', (string) $bound->getExtraValueType());
    }

    public function testATemplateWithoutABindingStays(): void
    {
        $bound = (new TemplateBinder())->bind($this->type('array<string, T>|B'), ['T' => Type::int()]);

        $this->assertSame('B|array<string, int>', (string) $bound);
    }

    public function testAUnionTakingMixedIsMixed(): void
    {
        $bound = (new TemplateBinder())->bind($this->type('T|int'), ['T' => Type::mixed()]);

        $this->assertSame('mixed', (string) $bound);
    }

    public function testAUnionTakesTheMembersOfAUnion(): void
    {
        $bound = (new TemplateBinder())->bind($this->type('T|string'), ['T' => Type::nullable(Type::union(Type::int(), Type::float()))]);

        $this->assertSame('float|int|null|string', (string) $bound);
    }

    public function testATypeThatCannotTakeTheBindingThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new TemplateBinder())->bind($this->type('B&\Countable'), ['B' => Type::int()]);
    }

    /**
     * @return iterable<string, array{string, bool}>
     */
    public static function templateCases(): iterable
    {
        yield 'a template' => ['T', true];
        yield 'a nullable template' => ['?T', true];
        yield 'a list' => ['list<T>', true];
        yield 'an array shape' => ['array{id: int, rest?: list<T>}', true];
        yield 'a union' => ['T|string', true];
        yield 'an intersection' => ['B&\Countable', true];
        yield 'a generic class' => ['Template<int, B>', true];
        yield 'a builtin' => ['int', false];
        yield 'a class' => ['\ArrayObject', false];
        yield 'a list without one' => ['list<string>', false];
        yield 'a generic class without one' => ['Template<int, \ArrayObject>', false];
    }

    #[DataProvider('templateCases')]
    public function testFindsATemplateAnywhereInAType(string $type, bool $expected): void
    {
        $this->assertSame($expected, (new TemplateBinder())->hasTemplate($this->type($type)));
    }

    /**
     * A type as written in a docblock of `Template`, which declares the templates `T` and `B`.
     */
    protected function type(string $type): Type
    {
        $typeContext = (new TypeContextFactory(new SymfonyStringTypeResolver()))->createFromClassName(Template::class);

        return (new StringTypeResolver())->resolve($type, $typeContext);
    }
}
