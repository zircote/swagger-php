<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Type;

use OpenApi\Specification\ComponentKey;
use OpenApi\Tests\Concerns\ExpectsLogEntries;
use OpenApi\Tests\Fixtures\Type\Box;
use OpenApi\Tests\Fixtures\Type\Number;
use OpenApi\Tests\Fixtures\Type\StampedBox;
use OpenApi\Tests\Fixtures\Type\Template;
use OpenApi\Type\Generics\TypeArguments;
use OpenApi\Type\TypeResolver;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\TypeInfo\Type;
use Symfony\Component\TypeInfo\TypeContext\TypeContext;
use Symfony\Component\TypeInfo\TypeResolver\TypeResolverInterface;

final class DocblockTemplateTest extends TestCase
{
    use ExpectsLogEntries;

    /**
     * @return iterable<string, array{string}>
     */
    public static function templateCases(): iterable
    {
        yield 'unbounded' => ['results'];
        yield 'bounded' => ['bounded'];
    }

    #[DataProvider('templateCases')]
    public function testClassTemplateIsNoUnknownClass(string $property): void
    {
        $resolver = new TypeResolver();
        $resolver->setLogger($this->trackingLogger());

        $type = $resolver->resolve(new \ReflectionProperty(Template::class, $property));

        $this->assertSame('array', $type?->type);
    }

    /**
     * Type-info reads an unbounded template as `mixed`, which an intersection does not take.
     */
    public function testAnIntersectionTypeInfoRejectsIsReported(): void
    {
        $this->expectLogEntry('Unsupported docblock type "(T & \\Countable)" on');

        $resolver = new TypeResolver();
        $resolver->setLogger($this->trackingLogger());

        $this->assertNull($resolver->getDocblockType(new \ReflectionProperty(Template::class, 'unbounded')));
    }

    /**
     * Type-info-extras up to 1.0.13 throws a `TypeError` here, then reads it as `class-string`.
     */
    public function testATemplateClassStringIsAString(): void
    {
        $this->allowLogEntry('Unsupported docblock type "class-string<T>" on');

        $resolver = new TypeResolver();
        $resolver->setLogger($this->trackingLogger());

        $this->assertSame('string', $resolver->resolve(new \ReflectionProperty(Template::class, 'kind'))?->type);
    }

    /**
     * Whatever the resolver throws, the docblock is reported and skipped.
     */
    public function testAnyResolverFailureIsReported(): void
    {
        $this->expectLogEntry('Unsupported docblock type "class-string<T>" on ' . Template::class . '::$kind', 'warning');

        $resolver = new class () extends TypeResolver {
            public function __construct()
            {
                parent::__construct();
                $this->docblockResolver = new class () implements TypeResolverInterface {
                    public function resolve(mixed $subject, ?TypeContext $typeContext = null): Type
                    {
                        throw new \TypeError('resolver failed');
                    }
                };
            }
        };
        $resolver->setLogger($this->trackingLogger());

        $this->assertSame('string', $resolver->resolve(new \ReflectionProperty(Template::class, 'kind'))?->type);
    }

    /**
     * Each component reads the member for itself, and the docblock is still read once.
     */
    public function testADocblockIsReportedOnceAcrossComponents(): void
    {
        $this->expectLogEntry('Unsupported docblock type "(T & \\Countable)" on');

        $resolver = new TypeResolver();
        $resolver->setLogger($this->trackingLogger());
        $property = new \ReflectionProperty(Template::class, 'unbounded');

        foreach ([Type::int(), Type::string(), Type::float()] as $argument) {
            $resolver->resolveIn($property, $this->key(Template::class, $argument, Type::int()));
        }
        $resolver->resolve($property);
    }

    public function testATemplateReadsAsItsArgumentInTheComponent(): void
    {
        $type = (new TypeResolver())->resolveIn(new \ReflectionProperty(Template::class, 'results'), $this->key(Template::class, Type::object(Number::class), Type::mixed()));

        $this->assertSame('array', $type?->type);
        $this->assertSame(Number::class, $type->items?->type);
    }

    public function testAnArrayShapeKeepsItsShape(): void
    {
        $type = (new TypeResolver())->resolveIn(new \ReflectionProperty(Template::class, 'shaped'), $this->key(Template::class, Type::string(), Type::mixed()));

        $this->assertSame('object', $type?->type);
        $this->assertSame('string', $type->properties['item']->type ?? null);
        $this->assertSame('integer', $type->properties['id']->type ?? null);
    }

    public function testATemplateTypedMixedIsNotNullableByItsDeclaredType(): void
    {
        $resolver = new TypeResolver();
        $property = new \ReflectionProperty(StampedBox::class, 'own');

        $this->assertNull($resolver->resolveIn($property, $this->key(StampedBox::class, Type::int()))?->nullable);
        $this->assertTrue($resolver->resolveIn($property, $this->key(StampedBox::class, Type::nullable(Type::int())))?->nullable);
    }

    public function testAMethodsOwnTemplateHidesTheClasss(): void
    {
        $resolver = new TypeResolver();
        $component = $this->key(Template::class, Type::int(), Type::mixed());

        $this->assertNull($resolver->resolveIn(new \ReflectionMethod(Template::class, 'same'), $component)?->type);
        $this->assertNull($resolver->resolveIn(new \ReflectionParameter([Template::class, 'same'], 'value'), $component)?->type);
        $this->assertSame('integer', $resolver->resolveIn(new \ReflectionProperty(Template::class, 'results'), $component)?->items?->type);
    }

    public function testAUnionTakingMixedIsMixed(): void
    {
        $type = (new TypeResolver())->resolveIn(new \ReflectionProperty(Template::class, 'either'), $this->key(Template::class, Type::mixed(), Type::mixed()));

        $this->assertNull($type?->type);
    }

    public function testAnArgumentTheTypeCannotTakeIsReported(): void
    {
        $this->expectLogEntry('Type "B&Countable" cannot take the type arguments, omitted in ' . Template::class . '::$counted');

        $resolver = new TypeResolver();
        $resolver->setLogger($this->trackingLogger());
        $type = $resolver->resolveIn(new \ReflectionProperty(Template::class, 'counted'), $this->key(Template::class, Type::int(), Type::int()));

        $this->assertNull($type?->type);
    }

    public function testOneArgumentForEachTemplateOrNoneIsBound(): void
    {
        $type = (new TypeResolver())->resolveIn(new \ReflectionProperty(Template::class, 'results'), $this->key(Template::class, Type::int()));

        $this->assertNull($type?->items?->type);
    }

    public function testOnlyAClassesOwnMembersBindItsTemplates(): void
    {
        $this->expectLogEntry('Type arguments of "' . StampedBox::class . '<int>" do not reach an inherited or trait member, ignored in ' . StampedBox::class . '::$stamp');
        $this->expectLogEntry('Type arguments of "' . StampedBox::class . '<int>" do not reach an inherited or trait member, ignored in ' . Box::class . '::$value');

        $resolver = new TypeResolver();
        $resolver->setLogger($this->trackingLogger());
        $key = $this->key(StampedBox::class, Type::int());

        $this->assertSame('integer', $resolver->resolveIn(new \ReflectionProperty(StampedBox::class, 'own'), $key)?->type);
        $this->assertNull($resolver->resolveIn(new \ReflectionProperty(StampedBox::class, 'stamp'), $key)?->type);
        $this->assertNull($resolver->resolveIn(new \ReflectionProperty(StampedBox::class, 'value'), $key)?->type);
        // asked again, it is not reported again
        $resolver->resolveIn(new \ReflectionProperty(StampedBox::class, 'stamp'), $key);
    }

    /**
     * The bindings of an inherited member read its docblock, so they are worked out on a miss only.
     */
    public function testAMemberAskedAgainInAComponentIsNotReadAgain(): void
    {
        $resolver = new class () extends TypeResolver {
            public int $reads = 0;

            protected function readDocblockType(\Reflector $reflector): ?Type
            {
                ++$this->reads;

                return parent::readDocblockType($reflector);
            }
        };
        $property = new \ReflectionProperty(StampedBox::class, 'stamp');
        $key = $this->key(StampedBox::class, Type::int());

        $resolver->resolveIn($property, $key);
        $reads = $resolver->reads;
        $resolver->resolveIn($property, $key);

        $this->assertSame($reads, $resolver->reads);
    }

    public function testAMemberReadWithoutTypeArgumentsKeepsItsTemplates(): void
    {
        $resolver = new TypeResolver();
        $property = new \ReflectionProperty(StampedBox::class, 'own');

        $this->assertSame('integer', $resolver->resolveIn($property, $this->key(StampedBox::class, Type::int()))?->type);
        $this->assertNull($resolver->resolve($property)?->type);
        $this->assertNull($resolver->resolveIn($property, new ComponentKey(StampedBox::class))?->type);
        $this->assertNull($resolver->resolveIn($property, null)?->type);
    }

    private function key(string $class, Type $argument, Type ...$arguments): ComponentKey
    {
        return new ComponentKey($class, typeArguments: new TypeArguments($argument, ...$arguments));
    }
}
