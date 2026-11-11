<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Type;

use OpenApi\Tests\Concerns\ExpectsLogEntries;
use OpenApi\Tests\Fixtures\Type\Template;
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
}
