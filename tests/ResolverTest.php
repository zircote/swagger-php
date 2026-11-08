<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Assembler;
use OpenApi\Augmenter;
use OpenApi\Builder;
use OpenApi\Builder\Mode;
use OpenApi\Contracts\ResolverInterface;
use OpenApi\Loggers\CollectingLogger;
use OpenApi\Resolver;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Specification\ComponentKey;
use OpenApi\Tests\Fixtures\ClassDescriptions;
use OpenApi\Utils\Pipeline;
use OpenApi\Utils\TypedList;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class ResolverTest extends TestCase
{
    /** @var list<string> */
    private array $deprecations = [];

    protected function setUp(): void
    {
        $this->deprecations = [];
        set_error_handler(function (int $errno, string $message): bool {
            if ($errno === E_USER_DEPRECATED) {
                $this->deprecations[] = $message;
            }

            return true;
        });
    }

    protected function tearDown(): void
    {
        restore_error_handler();
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

    public function testOnlyDocumentedPropertiesWithoutATypeAreChased(): void
    {
        $assembler = $this->assembler(Fixtures\Resolver\Catalogue::class);
        $spy = new Fixtures\Resolver\CountingResolver();

        $this->resolver(new Resolver\Reflection(), $spy)->resolve($assembler);

        $this->assertSame(['Catalogue'], $this->schemaNames($assembler));
        $this->assertSame([], $spy->attempts);
    }

    public function testAPropertyImpliedByASchemaIsChased(): void
    {
        $assembler = $this->assembler(Fixtures\Resolver\Shelf::class);

        $this->resolver(new Resolver\Reflection())->resolve($assembler);

        $this->assertSame(['Shelf', 'Manufacturer'], $this->schemaNames($assembler));
    }

    public function testAPromotedParameterIsChasedAndAnEnumIsNot(): void
    {
        $assembler = $this->assembler(Fixtures\Resolver\Crate::class);
        $spy = new Fixtures\Resolver\CountingResolver();

        $this->resolver(new Resolver\Reflection(), $spy)->resolve($assembler);

        $this->assertSame(['Crate', 'Manufacturer'], $this->schemaNames($assembler));
        $this->assertSame([], $spy->attempts);
    }

    public function testPropertyTypesAreChasedInDeclarationOrder(): void
    {
        $assembler = $this->assembler(Fixtures\Resolver\Pallet::class);

        $this->resolver(new Resolver\Reflection())->resolve($assembler);

        $this->assertSame(['Pallet', 'Manufacturer', 'Catalogue'], $this->schemaNames($assembler));
    }

    public function testADocumentedTraitPropertyIsChased(): void
    {
        $assembler = $this->assembler(Fixtures\Resolver\Bin::class);

        $this->resolver(new Resolver\Reflection())->resolve($assembler);

        $this->assertSame(['Bin', 'Manufacturer'], $this->schemaNames($assembler));
    }

    public function testATraitIsNotChasedForASchemaInheritanceLeavesAlone(): void
    {
        $assembler = $this->assembler();
        $assembler->getSpecification()->add((new OA\Schema())
            ->setReflector(new \ReflectionClass(Fixtures\Resolver\Bin::class))
            ->setMeta(Augmenter\Inheritance\Schemas::class, false));
        $spy = new Fixtures\Resolver\CountingResolver();

        $this->resolver(new Resolver\Reflection(), $spy)->resolve($assembler);

        $this->assertSame([], $spy->attempts);
    }

    /**
     * A discriminator map on the parent of a flattened class made the parent and its children
     * reference each other.
     */
    public function testAFlattenedHierarchyKeepsItsShape(): void
    {
        $document = (new Builder())
            ->setMode(Mode::SPEC)
            ->withResolver(fn (Resolver $resolver): Resolver => $resolver->withResolvers(
                fn (TypedList $resolvers): TypedList => $resolvers->add(new Fixtures\Resolver\FlatResolver()),
            ))
            ->addSource(new \ReflectionClass(Fixtures\Resolver\Checkout::class))
            ->withSpecification(fn (Specification $specification): Specification => $specification->add(new OA\Info(title: 'Resolver', version: '1.0.0')))
            ->withAugmenters(fn (Pipeline $augmenters): Pipeline => $augmenters->remove(Augmenter\Cleanup::class))
            ->build()
            ->toArray();

        $schemas = $document['components']['schemas'];
        $this->assertArrayNotHasKey('allOf', $schemas['CardPayment']);
        $this->assertArrayNotHasKey('allOf', $schemas['BankPayment']);
        $this->assertSame(['type', 'number'], array_keys($schemas['CardPayment']['properties']));
        $this->assertSame('#/components/schemas/CardPayment', $schemas['Payment']['discriminator']['mapping']['card']);
    }

    public function testASchemaBuiltElsewhereAsksForNothing(): void
    {
        $assembler = $this->assembler();
        // as a resolver describing the class from another source adds it: the class behind it,
        // properties without reflectors
        $assembler->getSpecification()->add((new OA\Schema(properties: [
            new OA\Property(property: 'manufacturer', schema: new OA\Schema(type: 'object')),
        ]))->setReflector(new \ReflectionClass(Fixtures\Resolver\Product::class)));
        $spy = new Fixtures\Resolver\CountingResolver();

        $this->resolver(new Resolver\Reflection(), $spy)->resolve($assembler);

        $this->assertSame([], $spy->attempts);
        $this->assertCount(1, $assembler->getSpecification()->schemas);
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
        $this->assertStringStartsWith('Class "' . Fixtures\Resolver\UnloadableStock::class . '" cannot be loaded, skipped: Failed to instantiate attribute', $logger->entries()[0]['message']);
    }

    public function testAKeyAwareResolverDescribesAClassForAQualifiedRef(): void
    {
        $assembler = $this->assembler(ClassDescriptions\Article::class);
        $groups = new Fixtures\Resolver\GroupsResolver();

        $this->resolver(new Resolver\Reflection(), $groups)->resolve($assembler);

        // the plain author has no attributes and no groups, so nothing describes it
        $this->assertSame(
            [ClassDescriptions\Article::class, ClassDescriptions\Author::class . '{"groups":["light"]}'],
            array_map(fn (OA\Schema $schema): string => (string) $schema->getComponentKey(), $assembler->getSpecification()->schemas),
        );
        $this->assertSame([
            ClassDescriptions\Author::class . '{"groups":["light"]}' => 1,
            ClassDescriptions\Author::class => 1,
        ], $groups->attempts);
    }

    public function testAClassNameResolverIsOnlyAskedAboutPlainKeys(): void
    {
        $assembler = $this->assembler(ClassDescriptions\Article::class);
        $spy = new Fixtures\Resolver\CountingResolver();

        $this->resolver($spy)->resolve($assembler);

        $this->assertSame([ClassDescriptions\Author::class => 1], $spy->attempts);
    }

    public function testAClassNameResolverIsReportedAsDeprecated(): void
    {
        $assembler = $this->assembler(Fixtures\Resolver\ProductController::class);

        $this->resolver(new Resolver\Reflection(), new Fixtures\Resolver\CountingResolver())->resolve($assembler);

        $this->assertSame([
            'Since zircote/swagger-php 7.2: ' . Fixtures\Resolver\CountingResolver::class . ' implements `ResolverInterface` alone, which is deprecated and will be removed in 8.0; implement `KeyResolverInterface`',
        ], $this->deprecations);
    }

    public function testReflectionLeavesAQualifiedKeyToTheNextResolver(): void
    {
        $assembler = $this->assembler();

        $claimed = (new Resolver\Reflection())->resolveKey(new ComponentKey(Fixtures\Resolver\Product::class, ['groups' => ['light']]), $assembler);

        $this->assertFalse($claimed);
        $this->assertSame([], $this->schemaNames($assembler));
    }

    public function testAQualifiedRefReferencesTheDescriptionItsResolverMade(): void
    {
        $document = (new Builder())
            ->setMode(Mode::SPEC)
            ->withResolver(fn (Resolver $resolver): Resolver => $resolver->withResolvers(
                fn (TypedList $resolvers): TypedList => $resolvers->add(new Fixtures\Resolver\GroupsResolver()),
            ))
            // nothing references the schemas from a path; keep them to assert on
            ->withAugmenters(fn (Pipeline $augmenters): Pipeline => $augmenters->remove(Augmenter\Cleanup::class))
            ->addSource(new \ReflectionClass(ClassDescriptions\Article::class))
            ->withSpecification(fn (Specification $specification): Specification => $specification->add(new OA\Info(title: 'Resolver', version: '1.0.0')))
            ->build()
            ->toArray();

        $this->assertSame('#/components/schemas/AuthorLight', $document['components']['schemas']['Article']['properties']['editor']['$ref']);
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
