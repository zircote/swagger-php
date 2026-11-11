<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Type;

use OpenApi\Specification\ComponentKey;
use OpenApi\Tests\Concerns\ExpectsLogEntries;
use OpenApi\Tests\Fixtures\Augmenter\GenericBox;
use OpenApi\Tests\Fixtures\Augmenter\GenericStampedBox;
use OpenApi\Tests\Fixtures\Type\Template;
use OpenApi\Type\TypeResolver;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\TypeInfo\Type;

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
     * Each copy reads the member in a component of its own, and the docblock is still read once.
     */
    public function testADocblockIsReportedOnceAcrossComponents(): void
    {
        $this->expectLogEntry('Unsupported docblock type "(T & \\Countable)" on');

        $resolver = new TypeResolver();
        $resolver->setLogger($this->trackingLogger());
        $property = new \ReflectionProperty(Template::class, 'unbounded');

        foreach (['int, int', 'string, int', 'float, int'] as $arguments) {
            $resolver->resolveIn($property, new ComponentKey(Template::class, ['of' => $arguments]));
        }
        $resolver->resolve($property);
    }

    public function testAnArrayShapeKeepsItsShape(): void
    {
        $resolver = new TypeResolver();
        $type = $resolver->resolveIn(new \ReflectionProperty(Template::class, 'shaped'), new ComponentKey(Template::class, ['of' => 'string, mixed']));

        $this->assertSame('object', $type?->type);
        $this->assertSame('string', $type->properties['item']->type ?? null);
        $this->assertSame('integer', $type->properties['id']->type ?? null);
    }

    public function testARefinedArgumentKeepsItsRefinement(): void
    {
        $resolver = new TypeResolver();
        $type = $resolver->resolveIn(new \ReflectionProperty(Template::class, 'results'), new ComponentKey(Template::class, ['of' => 'int<0, 100>, mixed']));

        $this->assertSame(0, $type?->items?->minimum);
        $this->assertSame(100, $type->items->maximum);
    }

    public function testATemplateReadsAsItsArgumentInTheComponent(): void
    {
        $resolver = new TypeResolver();
        $type = $resolver->resolveIn(new \ReflectionProperty(Template::class, 'results'), new ComponentKey(Template::class, ['of' => 'int, mixed']));

        $this->assertSame('array', $type?->type);
        $this->assertSame('integer', $type->items?->type);
    }

    public function testAMethodsOwnTemplateHidesTheClasss(): void
    {
        $resolver = new TypeResolver();
        $component = new ComponentKey(Template::class, ['of' => 'int, mixed']);

        $this->assertNull($resolver->resolveIn(new \ReflectionMethod(Template::class, 'same'), $component)?->type);
        $this->assertNull($resolver->resolveIn(new \ReflectionParameter([Template::class, 'same'], 'value'), $component)?->type);
        $this->assertSame('integer', $resolver->resolveIn(new \ReflectionProperty(Template::class, 'results'), $component)?->items?->type);
    }

    public function testAUnionTakingMixedIsMixed(): void
    {
        $resolver = new TypeResolver();
        $type = $resolver->resolveIn(new \ReflectionProperty(Template::class, 'either'), new ComponentKey(Template::class, ['of' => 'mixed, mixed']));

        $this->assertNull($type?->type);
    }

    public function testAnArgumentTheTypeCannotTakeIsReported(): void
    {
        $this->expectLogEntry('Type "B&Countable" cannot take the type arguments, omitted in ' . Template::class . '::$counted');

        $resolver = new TypeResolver();
        $resolver->setLogger($this->trackingLogger());
        $type = $resolver->resolveIn(new \ReflectionProperty(Template::class, 'counted'), new ComponentKey(Template::class, ['of' => 'int, int']));

        $this->assertNull($type?->type);
    }

    public function testOnlyAClassesOwnMembersBindItsTemplates(): void
    {
        $this->expectLogEntry('Type arguments of "' . GenericStampedBox::class . '<int>" do not reach an inherited or trait member, ignored in ' . GenericStampedBox::class . '::$stamp');
        $this->expectLogEntry('Type arguments of "' . GenericStampedBox::class . '<int>" do not reach an inherited or trait member, ignored in ' . GenericBox::class . '::$value');

        $resolver = new TypeResolver();
        $resolver->setLogger($this->trackingLogger());
        $key = new ComponentKey(GenericStampedBox::class, ['of' => 'int']);

        $this->assertSame('integer', $resolver->resolveIn(new \ReflectionProperty(GenericStampedBox::class, 'own'), $key)?->type);
        $this->assertNull($resolver->resolveIn(new \ReflectionProperty(GenericStampedBox::class, 'stamp'), $key)?->type);
        $this->assertNull($resolver->resolveIn(new \ReflectionProperty(GenericStampedBox::class, 'value'), $key)?->type);
        // asked again, it is not reported again
        $resolver->resolveIn(new \ReflectionProperty(GenericStampedBox::class, 'stamp'), $key);
    }

    /**
     * The bindings of an inherited member read its docblock, so they are worked out on a miss only.
     */
    public function testAMemberAskedAgainInAComponentIsNotReadAgain(): void
    {
        $resolver = new class () extends TypeResolver {
            public int $reads = 0;

            public function getDocblockType(\Reflector $reflector): ?Type
            {
                ++$this->reads;

                return parent::getDocblockType($reflector);
            }
        };
        $property = new \ReflectionProperty(GenericStampedBox::class, 'stamp');
        $key = new ComponentKey(GenericStampedBox::class, ['of' => 'int']);

        $resolver->resolveIn($property, $key);
        $reads = $resolver->reads;
        $resolver->resolveIn($property, $key);

        $this->assertSame($reads, $resolver->reads);
    }

    public function testAMemberReadOutsideAComponentKeepsItsTemplates(): void
    {
        $resolver = new TypeResolver();
        $property = new \ReflectionProperty(GenericStampedBox::class, 'own');

        $this->assertSame('integer', $resolver->resolveIn($property, new ComponentKey(GenericStampedBox::class, ['of' => 'int']))?->type);
        $this->assertNull($resolver->resolve($property)?->type);
        $this->assertNull($resolver->resolveIn($property, new ComponentKey(GenericStampedBox::class))?->type);
    }
}
