<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Assembler;
use OpenApi\Builder;
use OpenApi\Builder\Mode;
use OpenApi\Contracts\ResolverInterface;
use OpenApi\Resolver;
use OpenApi\Spec as OA;
use OpenApi\Utils\TypedList;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ResolverTest extends TestCase
{
    public function testResolvesRefAndTransitivePropertyTypeByDefault(): void
    {
        $assembler = $this->assembler(Fixtures\Resolver\ProductController::class);

        // Resolver\Reflection is registered by default
        (new Resolver())->resolve($assembler);

        // Product comes from the ref, Manufacturer from Product's property type
        $this->assertSame(['Product', 'Manufacturer'], $this->schemaNames($assembler));
    }

    public function testNoResolversIsNoop(): void
    {
        $assembler = $this->assembler(Fixtures\Resolver\ProductController::class);

        $this->resolver()->resolve($assembler);

        $this->assertSame([], $this->schemaNames($assembler));
    }

    public function testUnannotatedClassIsNotResolved(): void
    {
        $assembler = $this->assembler(Fixtures\Resolver\Product::class);

        $this->resolver(new Resolver\Reflection())->resolve($assembler);

        // Weight has no spec attributes and is left unresolved
        $this->assertSame(['Product', 'Manufacturer'], $this->schemaNames($assembler));
    }

    public function testFailedFqcnIsAttemptedOnlyOncePerRun(): void
    {
        $assembler = $this->assembler(Fixtures\Resolver\ProductController::class);
        $spy = new Fixtures\Resolver\CountingResolver();

        // Reflection keeps the loop going for several iterations (Product, then Manufacturer)
        $this->resolver(new Resolver\Reflection(), $spy)->resolve($assembler);

        // ... while Weight, which nothing can resolve, is only offered once
        $this->assertSame([Fixtures\Resolver\Weight::class => 1], $spy->attempts);
    }

    public static function fullBuildProvider(): iterable
    {
        yield 'single-controller' => [
            (new Builder())
                ->setMode(Mode::SPEC)
                ->addSource(new \ReflectionClass(Fixtures\Resolver\ProductController::class)),
        ];
        yield 'no-resolver' => [
            (new Builder())
                ->setMode(Mode::SPEC)
                ->withResolver(fn (Resolver $resolver): Resolver => $resolver->setResolvers(new TypedList()))
                ->addSource([
                    new \ReflectionClass(Fixtures\Resolver\Product::class),
                    new \ReflectionClass(Fixtures\Resolver\Manufacturer::class),
                    new \ReflectionClass(Fixtures\Resolver\ProductController::class),
                    new \ReflectionClass(Fixtures\Resolver\Weight::class),
                ]),
        ];
    }

    #[DataProvider('fullBuildProvider')]
    public function testFullBuild(Builder $builder): void
    {
        $result = $builder
            ->build();

        $spec = $result->toArray();

        $this->assertSame(
            ['Product', 'Manufacturer'],
            array_keys($spec['components']['schemas'])
        );
        $this->assertSame(
            '#/components/schemas/Product',
            $spec['paths']['/products']['get']['responses'][200]['content']['application/json']['schema']['$ref']
        );
    }

    protected function resolver(ResolverInterface ...$resolvers): Resolver
    {
        return new Resolver(new TypedList($resolvers));
    }

    /**
     * @param class-string ...$classes
     */
    protected function assembler(string ...$classes): Assembler
    {
        $assembler = new Assembler();
        foreach ($classes as $class) {
            $assembler->collect(new \ReflectionClass($class));
        }

        return $assembler;
    }

    /**
     * @return list<string>
     */
    protected function schemaNames(Assembler $assembler): array
    {
        return array_map(
            static fn (OA\Schema $schema): ?string => $schema->schema,
            $assembler->getSpecification()->schemas
        );
    }
}
