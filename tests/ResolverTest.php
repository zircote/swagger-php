<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Assembler;
use OpenApi\Augmenter\Cleanup;
use OpenApi\Builder;
use OpenApi\Builder\Mode;
use OpenApi\Contracts\DependencyProviderInterface;
use OpenApi\Contracts\ResolverInterface;
use OpenApi\Loggers\CollectingLogger;
use OpenApi\Resolver;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Utils\Pipeline;
use OpenApi\Utils\TypedList;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ResolverTest extends TestCase
{
    public function testDependencyProvidersShareDeduplicationAndTransitiveResolution(): void
    {
        $assembler = new Assembler();
        $operation = new OA\Operation\Get(path: '/metadata');
        $operation->setMeta('dependencies', [Fixtures\Resolver\Product::class, Fixtures\Resolver\Product::class]);
        $assembler->getSpecification()->add($operation);
        $spy = new Fixtures\Resolver\CountingResolver();
        $resolver = $this->resolver($spy, new Resolver\Reflection());
        $resolver->withDependencyProviders(fn (TypedList $providers): TypedList => $providers->add(new Fixtures\Resolver\MetadataDependencyProvider()));

        $resolver->resolve($assembler);

        $this->assertSame(['Product', 'Manufacturer'], $this->schemaNames($assembler));
        $this->assertSame([
            Fixtures\Resolver\Product::class => 1,
            Fixtures\Resolver\Manufacturer::class => 1,
            Fixtures\Resolver\Weight::class => 1,
        ], $spy->attempts);
    }

    public function testProvidersDiscoverMetadataOnNewlyCollectedComponents(): void
    {
        $assembler = new Assembler();
        $resolver = new Resolver();
        $resolver->withDependencyProviders(fn (TypedList $providers): TypedList => $providers->add(new class () implements DependencyProviderInterface {
            public function dependencies(Specification $specification): iterable
            {
                yield Fixtures\Resolver\MetadataContainer::class;
                foreach ($specification->schemas as $schema) {
                    if ($schema->getClassName() === Fixtures\Resolver\MetadataContainer::class) {
                        yield Fixtures\Resolver\Product::class;
                    }
                }
            }
        }));

        $resolver->resolve($assembler);

        $this->assertSame(['MetadataContainer', 'Product', 'Manufacturer'], $this->schemaNames($assembler));
    }

    public function testProviderNamesDoNotHaveToExistAsPhpClasses(): void
    {
        $assembler = new Assembler();
        $resolver = $this->resolver(new class () implements ResolverInterface {
            public function resolve(string $fqcn, Assembler $assembler): bool
            {
                if ($fqcn !== 'ExternalModel') {
                    return false;
                }
                $schema = new OA\Schema(type: 'object', component: 'ExternalModel');
                $assembler->getSpecification()->add($schema);

                return true;
            }
        });
        $resolver->withDependencyProviders(fn (TypedList $providers): TypedList => $providers->add(new class () implements DependencyProviderInterface {
            public function dependencies(Specification $specification): iterable
            {
                yield 'ExternalModel';
            }
        }));

        $resolver->resolve($assembler);

        $this->assertSame(['ExternalModel'], $this->schemaNames($assembler));
    }

    public function testMultipleProvidersUseTheSameListAndDeclinedNamesAreAttemptedOnce(): void
    {
        $assembler = new Assembler();
        $operation = new OA\Operation\Get(path: '/metadata');
        $operation->setMeta('dependencies', [Fixtures\Resolver\Weight::class]);
        $assembler->getSpecification()->add($operation);
        $spy = new Fixtures\Resolver\CountingResolver();
        $resolver = $this->resolver($spy, new Resolver\Reflection());
        $resolver->withDependencyProviders(fn (TypedList $providers): TypedList => $providers->add(new Fixtures\Resolver\MetadataDependencyProvider()));
        $resolver->withDependencyProviders(fn (TypedList $providers): TypedList => $providers->add(new class () implements DependencyProviderInterface {
            public function dependencies(Specification $specification): iterable
            {
                yield Fixtures\Resolver\Product::class;
            }
        }));

        $resolver->resolve($assembler);

        $this->assertSame(['Product', 'Manufacturer'], $this->schemaNames($assembler));
        $this->assertSame(1, $spy->attempts[Fixtures\Resolver\Weight::class]);
    }

    public function testNoResolversAlsoDisablesProviderCollection(): void
    {
        $assembler = new Assembler();
        $resolver = $this->resolver();
        $resolver->withDependencyProviders(fn (TypedList $providers): TypedList => $providers->add(new class () implements DependencyProviderInterface {
            public function dependencies(Specification $specification): iterable
            {
                throw new \LogicException('Discovery must not run when resolution is disabled');
            }
        }));

        $resolver->resolve($assembler);

        $this->assertSame([], $this->schemaNames($assembler));
    }

    public function testProvidersSeeAllSpecificationContributions(): void
    {
        $result = (new Builder())
            ->setMode(Mode::SPEC)
            ->withResolver(fn (Resolver $resolver): Resolver => $resolver->withDependencyProviders(
                fn (TypedList $providers): TypedList => $providers->add(new Fixtures\Resolver\MetadataDependencyProvider()),
            ))
            ->withSpecification(function (Specification $specification): void {
                $operation = new OA\Operation\Get(path: '/metadata', responses: [new OA\Response(response: 200, description: 'OK')]);
                $operation->setMeta('dependencies', [Fixtures\Resolver\Product::class]);
                $specification->add($operation);
            })
            ->withSpecification(function (Specification $specification): void {
                $specification->info = new OA\Info(title: 'Metadata', version: '1.0');
            })
            ->withAugmenters(function (Pipeline $pipeline): void {
                $pipeline->get(Cleanup::class)->setEnabled(false);
            })
            ->build();

        $this->assertSame(['Product', 'Manufacturer'], array_keys($result->toArray()['components']['schemas']));
        $this->assertSame('#/components/schemas/Manufacturer', $result->toArray()['components']['schemas']['Product']['properties']['manufacturer']['$ref']);
    }

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

    /**
     * A class reached by resolving a reference costs the run that class, not the whole run,
     * when an attribute on it fails to instantiate — as it does when the scan reaches it.
     */
    public function testUnloadableClassIsSkippedAndTheRestResolved(): void
    {
        $assembler = $this->assembler(Fixtures\Resolver\Warehouse::class);
        $logger = new CollectingLogger();

        $resolver = new Resolver();
        $resolver->setLogger($logger);
        $resolver->resolve($assembler);

        $this->assertSame(['Warehouse', 'Manufacturer'], $this->schemaNames($assembler));
        $this->assertCount(1, $logger->entries());
        $this->assertStringStartsWith('Skipping unloadable ' . Fixtures\Resolver\UnloadableStock::class . ': Failed to instantiate attribute', $logger->entries()[0]['message']);
    }

    public function testALoggerAwareResolverGetsTheResolversLogger(): void
    {
        $assembler = $this->assembler(Fixtures\Resolver\Product::class);
        $logger = new CollectingLogger();

        $resolver = $this->resolver(new Fixtures\Resolver\LoggingResolver());
        $resolver->setLogger($logger);
        $resolver->resolve($assembler);

        $this->assertSame([
            'Declined ' . Fixtures\Resolver\Manufacturer::class,
            'Declined ' . Fixtures\Resolver\Weight::class,
        ], array_column($logger->entries(), 'message'));
    }

    public function testALoggerAwareResolverLogsToTheBuildersLogger(): void
    {
        $logger = new CollectingLogger();

        (new Builder())
            ->setMode(Mode::SPEC)
            ->setLogger($logger)
            ->withResolver(fn (Resolver $resolver): Resolver => $resolver->withResolvers(
                fn (TypedList $resolvers): TypedList => $resolvers->add(new Fixtures\Resolver\LoggingResolver()),
            ))
            ->addSource(new \ReflectionClass(Fixtures\Resolver\ProductController::class))
            ->build();

        $this->assertContains('Declined ' . Fixtures\Resolver\Weight::class, array_column($logger->entries(), 'message'));
    }

    /**
     * @return iterable<mixed>
     */
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
        return new Resolver(new TypedList(array_values($resolvers)));
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
            static fn (OA\Schema $schema): ?string => $schema->component,
            $assembler->getSpecification()->schemas
        );
    }
}
