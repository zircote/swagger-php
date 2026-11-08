<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Assembler;
use OpenApi\Augmenter;
use OpenApi\Augmenter\ImpliedSchemas;
use OpenApi\Augmenter\OperationIds;
use OpenApi\Augmenter\Types;
use OpenApi\Builder;
use OpenApi\Builder\Hooks;
use OpenApi\Builder\Mode;
use OpenApi\Contracts\KeyResolverInterface;
use OpenApi\Contracts\PluginInterface;
use OpenApi\Examples\Specs\Webhooks\Attributes as WebhooksAttributes;
use OpenApi\Examples\Specs\Webhooks\Spec as WebhooksSpec;
use OpenApi\Generator;
use OpenApi\Loggers\CollectingLogger;
use OpenApi\Merge\LastWins;
use OpenApi\OpenApiException;
use OpenApi\Resolver;
use OpenApi\Resolver\Concerns\KeyResolverTrait;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Specification\ComponentKey;
use OpenApi\Tests\Concerns\UsesExamples;
use OpenApi\Type\TypeResolver;
use OpenApi\Utils\PipeInterface;
use OpenApi\Utils\Pipeline;
use OpenApi\Utils\SourceFinder;
use OpenApi\Utils\TypedList;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

final class BuilderTest extends OpenApiTestCase
{
    use UsesExamples;

    public function testBuild(): void
    {
        $this->registerExampleClassloader('petstore');

        $result = (new Builder())
            ->setMode(Mode::CLASSIC)
            ->addSource(self::examplePath('petstore/annotations'))
            ->setLogger($this->trackingLogger())
            ->withGenerator(function (Generator $generator): void {
                $generator->setAnalyser($this->getAnalyzer());
                $generator->setTypeResolver($this->getTypeResolver());
            })
            ->build();

        $this->assertTrue($result->isValid());
        $this->assertNotEmpty($result->toArray());
        $this->assertNotEmpty($result->toJson());
        $this->assertNotEmpty($result->toYaml());
    }

    public function testIsValidReportsCompilerErrors(): void
    {
        // no OA\Info anywhere -> the compiler reports "Document is missing info"
        $this->expectLogEntry('Document is missing info', 'error');

        $result = (new Builder())
            ->setMode(Mode::SPEC)
            ->addSource((string) self::fixture('Assembler/SimpleController.php'))
            ->setLogger($this->trackingLogger())
            ->build();

        $this->assertNotEmpty($result->toArray(), 'a document is still produced');
        $this->assertFalse($result->isValid(), 'errors must make the result invalid');
    }

    public function testBuildMatchesGenerator(): void
    {
        $this->registerExampleClassloader('petstore');
        $sourceDir = self::examplePath('petstore/annotations');

        $generatorOutput = (new Generator())
            ->setAnalyser($this->getAnalyzer())
            ->setTypeResolver($this->getTypeResolver())
            ->generate([$sourceDir]);

        $builderResult = (new Builder())
            ->setMode(Mode::CLASSIC)
            ->addSource($sourceDir)
            ->withGenerator(function (Generator $generator): void {
                $generator->setAnalyser($this->getAnalyzer());
                $generator->setTypeResolver($this->getTypeResolver());
            })
            ->build();

        $this->assertSpecEquals($generatorOutput?->toYaml(), $builderResult->toYaml());
    }

    public function testBuildWithVersion(): void
    {
        $this->registerExampleClassloader('petstore');

        $result = (new Builder())
            ->setMode(Mode::CLASSIC)
            ->addSource(self::examplePath('petstore/annotations'))
            ->setVersion('3.1.0')
            ->withGenerator(function (Generator $generator): void {
                $generator->setAnalyser($this->getAnalyzer());
                $generator->setTypeResolver($this->getTypeResolver());
            })
            ->build();

        $this->assertTrue($result->isValid());
        $spec = $result->toArray();
        $this->assertSame('3.1.0', $spec['openapi']);
    }

    public function testBuildWithFinder(): void
    {
        $this->registerExampleClassloader('petstore');

        $result = (new Builder())
            ->setMode(Mode::CLASSIC)
            ->addSource(new SourceFinder(self::examplePath('petstore/annotations')))
            ->withGenerator(function (Generator $generator): void {
                $generator->setAnalyser($this->getAnalyzer());
                $generator->setTypeResolver($this->getTypeResolver());
            })
            ->build();

        $this->assertTrue($result->isValid());
    }

    public function testBuildFiles(): void
    {
        $this->registerExampleClassloader('petstore');

        $result = (new Builder())
            ->setMode(Mode::CLASSIC)
            ->addSource(self::examplePath('petstore/annotations'))
            ->withGenerator(function (Generator $generator): void {
                $generator->setAnalyser($this->getAnalyzer());
                $generator->setTypeResolver($this->getTypeResolver());
            })
            ->build();

        $this->assertNotEmpty($result->files());
        foreach ($result->files() as $file) {
            $this->assertFileExists($file);
        }
    }

    public function testBuildReflectors(): void
    {
        $this->registerExampleClassloader('webhooks', 'spec');

        $reflectorResult = (new Builder())
            ->setMode(Mode::SPEC)
            ->addSource([
                new \ReflectionClass(WebhooksSpec\NewPetWebhook::class),
                new \ReflectionClass(WebhooksSpec\OpenApiSpec::class),
                new \ReflectionClass(WebhooksSpec\Pet::class),
            ])
            ->build();

        $fileResult = (new Builder())
            ->setMode(Mode::SPEC)
            ->addSource(new SourceFinder(self::examplePath('webhooks/spec')))
            ->build();

        $this->assertSpecEquals($fileResult->toArray(), $reflectorResult->toArray());
    }

    public function testBuildReflectorsHybrid(): void
    {
        $this->registerExampleClassloader('webhooks', 'attributes');

        $reflectorResult = (new Builder())
            ->setMode(Mode::HYBRID)
            ->addSource([
                new \ReflectionClass(WebhooksAttributes\OpenApiSpec::class),
                new \ReflectionClass(WebhooksAttributes\Pet::class),
            ])
            ->build();

        $fileResult = (new Builder())
            ->setMode(Mode::HYBRID)
            ->addSource(new SourceFinder(self::examplePath('webhooks/attributes')))
            ->build();

        // the info lives on a classic attribute, so only the classic half can have read it
        $this->assertSame('Webhook Example', $reflectorResult->toArray()['info']['title'] ?? null);
        $this->assertSpecEquals($fileResult->toArray(), $reflectorResult->toArray());
    }

    public function testHybridOrdersTheTopLevelKeysAsClassic(): void
    {
        $build = static fn (Mode $mode): array => array_keys((new Builder())
            ->setMode($mode)
            ->addSource(new SourceFinder(__DIR__ . '/Fixtures/KeyOrder'))
            ->build()
            ->toArray());

        $this->assertSame(
            ['openapi', 'info', 'servers', 'paths', 'components', 'security', 'tags', 'externalDocs', 'webhooks'],
            $build(Mode::CLASSIC),
        );
        $this->assertSame($build(Mode::CLASSIC), $build(Mode::HYBRID));
    }

    /**
     * A class from `eval()` has no file to scan. Spec reads its attributes through reflection
     * alone, and the classic half of a hybrid build, which needs the file, skips it.
     */
    public function testBuildReadsAClassFromEval(): void
    {
        static $instance = null;
        $instance ??= eval('namespace OpenApi\Tests;
            use OpenApi\Attributes as OAT;
            use OpenApi\Spec as OA;
            class EvaluatedController {
                #[OAT\Get(path: "/classic", responses: [new OAT\Response(response: 200, description: "ok")])]
                public function classic() {}
                #[OA\Operation\Get(path: "/spec", responses: [new OA\Response(response: 200, description: "ok")])]
                public function spec() {}
            }
            return new EvaluatedController();');
        $class = $instance::class;

        $this->expectLogEntry("Skipping {$class}: its source file ", 'warning');
        $this->allowLogEntry('Document is missing info');

        foreach ([Mode::SPEC, Mode::HYBRID] as $mode) {
            $result = (new Builder())
                ->setMode($mode)
                ->addSource(new \ReflectionClass($instance))
                ->setLogger($this->trackingLogger())
                ->build();

            $this->assertSame(['/spec'], array_keys($result->toArray()['paths']), $mode->value);
        }
    }

    public function testBuildReportsANonClassReflectorSpec(): void
    {
        $this->expectLogEntry('Source "' . Fixtures\Assembler\SimpleController::class . '::', 'error');
        $this->allowLogEntry('Document is missing info', 'Document needs at least one of paths, webhooks or components');

        $method = (new \ReflectionClass(Fixtures\Assembler\SimpleController::class))->getMethods()[0];

        $result = (new Builder())
            ->setMode(Mode::SPEC)
            ->addSource($method)
            ->setLogger($this->trackingLogger())
            ->build();

        $this->assertArrayNotHasKey('paths', $result->toArray());
    }

    public function testBuildEmptySources(): void
    {
        $this->expectLogEntry('Required @OA\Info() not found');
        $this->expectLogEntry('Required @OA\PathItem() not found');

        $result = (new Builder())
            ->setMode(Mode::CLASSIC)
            ->setSources([])
            ->setLogger($this->trackingLogger())
            ->build();

        $this->assertEmpty($result->files());
    }

    public function testBuildLogsClassicWarnings(): void
    {
        $this->expectLogEntry('Required @OA\Info() not found', 'warning');
        $this->expectLogEntry('Required @OA\PathItem() not found', 'warning');

        (new Builder())
            ->setMode(Mode::CLASSIC)
            ->setSources([])
            ->setLogger($this->trackingLogger())
            ->build();
    }

    /**
     * Spec reads only a constructor's parameters, as promoted properties. Anything else on it is
     * left out of the document, with a warning naming the attributes and where to put them.
     */
    public function testBuildReportsConstructorAttributes(): void
    {
        require_once self::fixture('PHP/ConstructorAttributes.php');

        $result = (new Builder())
            ->setMode(Mode::SPEC)
            ->addSource(self::fixture('PHP/ConstructorAttributes.php'))
            ->setLogger($this->trackingLogger())
            ->build();

        $this->assertArrayNotHasKey('/constructor', $result->toArray()['paths']);
        $this->assertArrayHasKey('/method', $result->toArray()['paths']);

        $this->expectLogEntry('on a constructor, ignored', 'error');

        $reported = array_column($this->recordedLogEntries(), 'message');
        $this->assertStringStartsWith(
            'OpenApi\Spec\Operation\Post, OpenApi\Spec\Response on a constructor, ignored; declare them on the class in ',
            $reported[0],
        );
        $this->assertStringContainsString('ConstructorAttributesController::__construct()', $reported[0]);
    }

    /**
     * A component declared on a member is one only in a class carrying `#[OA\Components]`.
     * Without it, a keyed one is left out of the document, with a warning naming the member.
     */
    public function testBuildReportsStrayComponents(): void
    {
        require_once self::fixture('PHP/StrayComponents.php');

        $result = (new Builder())
            ->setMode(Mode::SPEC)
            ->addSource(self::fixture('PHP/StrayComponents.php'))
            ->setLogger($this->trackingLogger())
            ->build();

        $this->assertArrayNotHasKey('components', $result->toArray());

        $this->expectLogEntry('Schema "onProperty" is declared on a member without OpenApi\Spec\Components on its class, ignored in ' . Fixtures\PHP\StrayComponents::class . '::$property in ', 'error');
        $this->expectLogEntry('Schema "onMethod" is declared on a member without OpenApi\Spec\Components on its class, ignored in ' . Fixtures\PHP\StrayComponents::class . '::method() in ', 'error');
        // what referred to them is left pointing nowhere
        $this->expectLogEntry('Reference "#/components/schemas/onProperty" points to no component', 'error');
        $this->expectLogEntry('Reference "#/components/schemas/onMethod" points to no component', 'error');
    }

    /**
     * A class that exists by name but will not load — its parent is missing — must cost
     * the scan one class, not the whole run: https://github.com/zircote/swagger-php/issues/1600.
     */
    public function testBuildSkipsClassThatWillNotLoadClassic(): void
    {
        $this->expectLogEntry(
            'Skipping unloadable OpenApi\Tests\Fixtures\PHP\UnloadableClassRepro\BrokenStub: Class "OpenApi\Tests\Fixtures\PHP\UnloadableClassRepro\DeliberatelyAbsentParent" not found',
            'warning',
        );

        $result = (new Builder())
            ->setMode(Mode::CLASSIC)
            ->addSource(self::fixture('PHP/UnloadableClassRepro'))
            ->setLogger($this->trackingLogger())
            ->build();

        $this->assertStringContainsString('getEndpoint', $result->toYaml());
    }

    public function testBuildSkipsClassThatWillNotLoadHybrid(): void
    {
        // hybrid scans twice — the spec-side Assembler pass and doHybridAssemble()'s
        // classic pass — so the broken class is independently skipped, and reported, by each
        $this->expectLogEntry('cannot be loaded, skipped', 'error');
        $this->expectLogEntry('Skipping unloadable', 'warning');

        $result = (new Builder())
            ->setMode(Mode::HYBRID)
            ->addSource(self::fixture('PHP/UnloadableClassRepro'))
            ->setLogger($this->trackingLogger())
            ->build();

        $this->assertStringContainsString('getEndpoint', $result->toYaml());
    }

    public function testBuildSkipsClassThatWillNotLoadSpec(): void
    {
        $this->expectLogEntry('cannot be loaded, skipped', 'error');
        $this->allowLogEntry('Document is missing info', 'Document needs at least one of paths, webhooks or components');

        (new Builder())
            ->setMode(Mode::SPEC)
            ->addSource(self::fixture('PHP/UnloadableClassRepro'))
            ->setLogger($this->trackingLogger())
            ->build();
    }

    /**
     * The same holds for a class the scan never sees, reached by resolving a reference to it.
     */
    /**
     * The attribute that fails costs its member: the class keeps its schema, without `unit`.
     */
    public function testBuildSkipsTheMemberOfAReferencedClassThatWillNotLoadSpec(): void
    {
        $this->expectLogEntry('Parameter $unit of ' . Fixtures\Resolver\UnloadableStock::class . '::__construct() cannot be read, skipped: Failed to instantiate attribute', 'error');
        $this->allowLogEntry('Document is missing info', 'Document needs at least one of paths, webhooks or components', 'Reference "');

        $result = (new Builder())
            ->setMode(Mode::SPEC)
            ->addSource(new \ReflectionClass(Fixtures\Resolver\Warehouse::class))
            // nothing references the schemas from a path; keep them to assert on
            ->withAugmenters(static function (Pipeline $augmenters): void {
                $augmenters->get(Augmenter\Cleanup::class)?->setEnabled(false);
            })
            ->setLogger($this->trackingLogger())
            ->build();

        $schemas = $result->toArray()['components']['schemas'];
        $this->assertArrayHasKey('Manufacturer', $schemas);
        $this->assertEquals(new \stdClass(), $schemas['UnloadableStock']);
    }

    /**
     * A skipped class is reported to the attached logger as an error, since what it declares is
     * lost, and the document left behind is built from the rest. Both skips: the scan's, for a
     * class that will not load, and assembly's, for one whose attributes fail.
     */
    public function testBuildLogsSkippedClassesAsErrorsSpec(): void
    {
        $this->expectLogEntry('Class "' . Fixtures\PHP\SkippedClasses\WillNotReflect::class . '" cannot be loaded, skipped: ', 'error');
        $this->expectLogEntry('Class "' . Fixtures\PHP\SkippedClasses\WillNotCollect::class . '" cannot be loaded, skipped: ', 'error');

        $result = (new Builder())
            ->setMode(Mode::SPEC)
            ->addSource(self::fixture('PHP/SkippedClasses'))
            ->setLogger($this->trackingLogger())
            ->build();

        $this->assertSame(['/endpoint'], array_keys($result->toArray()['paths']));
        $this->assertFalse($result->isValid());
    }

    public function testCompilerDiagnosticsReachTheConfiguredLogger(): void
    {
        $logger = new CollectingLogger();

        // spec mode: an empty source set leaves the compiler with nothing to validate
        (new Builder())
            ->setMode(Mode::SPEC)
            ->setSources([])
            ->setVersion('3.0.0')
            ->setLogger($logger)
            ->build();

        $this->assertContains('Document is missing info', array_column($logger->entries(), 'message'));
    }

    public function testALoggerPassedToTheConstructorReceivesTheBuild(): void
    {
        $logger = new CollectingLogger();

        (new Builder($logger))
            ->setMode(Mode::SPEC)
            ->setSources([])
            ->setVersion('3.0.0')
            ->build();

        $this->assertContains('Document is missing info', array_column($logger->entries(), 'message'));
    }

    public function testASubclassWithAConstructorOfItsOwnBuilds(): void
    {
        $builder = new class () extends Builder {
            public function __construct()
            {
            }
        };

        $result = $builder->setMode(Mode::SPEC)->setSources([])->setVersion('3.0.0')->build();

        $this->assertContains('Document is missing info', array_column($result->log(), 'message'));
    }

    public function testAnErrorLoggedDuringTheBuildMakesTheResultInvalid(): void
    {
        $result = (new Builder())
            ->setMode(Mode::SPEC)
            ->setSources([])
            ->withSpecification(function (Specification $specification): void {
                $specification->info = new OA\Info(title: 'T', version: '1.0');
            })
            ->withAugmenters(fn (Pipeline $augmenters): Pipeline => $augmenters->add(new class () implements PipeInterface, LoggerAwareInterface {
                use LoggerAwareTrait;

                public function __invoke(mixed $specification): mixed
                {
                    $this->logger?->error('cannot augment');

                    return $specification;
                }

                public function group(): \BackedEnum
                {
                    return Augmenter\Group::Augment;
                }
            }))
            ->build();

        $this->assertFalse($result->isValid());
    }

    public function testGetAugmentersReturnsDefaultPipeline(): void
    {
        $builder = new Builder();
        $pipeline = $builder->getAugmenters();

        $found = false;
        $pipeline->walk(function (callable $pipe) use (&$found): void {
            if ($pipe instanceof OperationIds) {
                $found = true;
            }
        });

        $this->assertTrue($found, 'Default augmenters should include OperationId');
    }

    public function testPipelineGetReturnsTypedInstance(): void
    {
        $builder = new Builder();
        $operationId = $builder->getAugmenters()->get(OperationIds::class);

        $this->assertInstanceOf(OperationIds::class, $operationId);

        $operationId->setHash(false);

        $rc = new \ReflectionProperty($operationId, 'hash');
        $this->assertFalse($rc->getValue($operationId));
    }

    public function testWhatYouAddGetsTheBuildersServices(): void
    {
        $augmenter = new Fixtures\ServiceRecorder();
        $resolver = new Fixtures\ServiceRecorder();
        $merger = new Fixtures\ServiceRecorder();
        $namer = new Fixtures\PrefixedComponentNamer();

        $builder = (new Builder())
            ->setMode(Mode::SPEC)
            ->setComponentNamer($namer)
            ->withAugmenters(fn (Pipeline $augmenters): Pipeline => $augmenters->add($augmenter))
            ->withResolver(fn (Resolver $chain): Resolver => $chain->withResolvers(fn (TypedList $resolvers): TypedList => $resolvers->add($resolver)))
            ->withMergers(fn (TypedList $mergers): TypedList => $mergers->insert($merger, LastWins::class))
            ->addSource([new \ReflectionClass(Fixtures\Augmenter\BasicEnum::class)])
            ->setLogger($this->trackingLogger());
        $this->allowLogEntry('Document is missing info', 'Document needs at least one of paths, webhooks or components', 'unreferenced');
        $builder->build();

        foreach (['augmenter' => $augmenter, 'resolver' => $resolver, 'merger' => $merger] as $role => $recorder) {
            $this->assertSame($namer, $recorder->namer, $role);
            $this->assertSame($builder->getAttributeFactory(), $recorder->attributeFactory, $role);
            $this->assertSame($this->typeResolverOf($builder->getAugmenters()->get(Types::class)), $recorder->typeResolver, $role);
        }
    }

    public function testTheAugmentersReadingTypesShareOneResolver(): void
    {
        $builder = (new Builder())
            ->setMode(Mode::SPEC)
            ->addSource([new \ReflectionClass(Fixtures\Augmenter\BasicEnum::class)])
            ->setLogger($this->trackingLogger());
        $this->allowLogEntry('Document is missing info', 'Document needs at least one of paths, webhooks or components', 'unreferenced');
        $builder->build();

        $resolver = $this->typeResolverOf($builder->getAugmenters()->get(Types::class));
        $this->assertInstanceOf(TypeResolver::class, $resolver);
        $this->assertSame($resolver, $this->typeResolverOf($builder->getAugmenters()->get(ImpliedSchemas::class)));
    }

    public function testATypeResolverSetOnTheBuilderReachesTheAugmentersReadingTypes(): void
    {
        $resolver = new TypeResolver();
        $builder = (new Builder())
            ->setMode(Mode::SPEC)
            ->setTypeResolver($resolver)
            ->addSource([new \ReflectionClass(Fixtures\Augmenter\BasicEnum::class)])
            ->setLogger($this->trackingLogger());
        $this->allowLogEntry('Document is missing info', 'Document needs at least one of paths, webhooks or components', 'unreferenced');
        $builder->build();

        $this->assertSame($resolver, $this->typeResolverOf($builder->getAugmenters()->get(Types::class)));
        $this->assertSame($resolver, $this->typeResolverOf($builder->getAugmenters()->get(ImpliedSchemas::class)));
    }

    /**
     * `ImpliedSchemas` reads the member first. `Types` gets the shared resolver's answer again.
     */
    public function testAnUnreadableDocblockTypeIsReportedOnceInABuild(): void
    {
        $this->expectLogEntry('Unsupported docblock type "NoSuchClass" on ' . Fixtures\Augmenter\UnreadableDocblockType::class . '::$unknown', 'warning');
        $this->allowLogEntry('Document is missing info', 'Document needs at least one of paths, webhooks or components', 'unreferenced');

        (new Builder())
            ->setMode(Mode::SPEC)
            ->addSource(new \ReflectionClass(Fixtures\Augmenter\UnreadableDocblockType::class))
            ->setLogger($this->trackingLogger())
            ->build();
    }

    public function testComponentNamerNamesEveryComponentAlike(): void
    {
        $specification = (new Builder())
            ->setMode(Mode::SPEC)
            ->setComponentNamer(new Fixtures\PrefixedComponentNamer())
            // nothing references the schemas, so keep them
            ->withAugmenters(fn (Pipeline $augmenters): Pipeline => $augmenters->remove(Augmenter\Cleanup::class))
            ->addSource([
                new \ReflectionClass(Fixtures\Augmenter\Hierarchy\Spec\ParentWithSchema::class),
                new \ReflectionClass(Fixtures\Augmenter\Hierarchy\Spec\ChildOfParentWithSchema::class),
                new \ReflectionClass(Fixtures\Augmenter\BasicEnum::class),
            ])
            ->build()
            ->specification();
        $this->assertInstanceOf(Specification::class, $specification);

        $schemas = [];
        foreach ($specification->schemas as $schema) {
            $schemas[(string) $schema->component] = $schema;
        }
        ksort($schemas);

        $this->assertSame(['ApiBasicEnum', 'ApiChildOfParentWithSchema', 'ApiParentWithSchema'], array_keys($schemas));
        $this->assertSame('#/components/schemas/ApiParentWithSchema', $schemas['ApiChildOfParentWithSchema']->allOf[0]->ref ?? null);
    }

    /**
     * @return iterable<string, array{array<array-key, mixed>, list<string>}>
     */
    public static function plugins(): iterable
    {
        yield 'class' => [[Fixtures\ServerPlugin::class], ['https://plugin.example']];
        yield 'class with settings' => [[Fixtures\ServerPlugin::class => ['url' => 'https://configured.example']], ['https://configured.example']];
        yield 'instance' => [[new Fixtures\ServerPlugin()], ['https://plugin.example']];
        yield 'several, in order' => [[Fixtures\ServerPlugin::class, new Fixtures\ServerPlugin(), Fixtures\ServerPlugin::class => ['url' => 'https://last.example']], ['https://plugin.example', 'https://plugin.example', 'https://last.example']];
    }

    /**
     * @param array<array-key, mixed> $plugins
     * @param list<string>            $expected
     */
    #[DataProvider('plugins')]
    public function testPluginsConfigureTheBuilder(array $plugins, array $expected): void
    {
        $specification = new Specification();

        (new Builder())
            ->setMode(Mode::SPEC)
            ->withPlugins($plugins)
            ->withSpecification(function (Specification $assembled) use (&$specification): void {
                $specification = $assembled;
            })
            ->build();

        $this->assertSame($expected, array_map(static fn (OA\Server $server): ?string => $server->url, $specification->servers));
    }

    public function testPluginsRunInTheOrderGiven(): void
    {
        $first = new class () implements KeyResolverInterface {
            use KeyResolverTrait;

            public function resolveKey(ComponentKey $key, Assembler $assembler): bool
            {
                return false;
            }
        };
        $second = clone $first;
        $plugin = static fn (KeyResolverInterface $resolver): PluginInterface => new class ($resolver) implements PluginInterface {
            public function __construct(protected KeyResolverInterface $resolver)
            {
            }

            public function configure(Hooks $hooks, array $config): void
            {
                $hooks->withResolver(fn (Resolver $resolver): Resolver => $resolver->withResolvers(fn (TypedList $resolvers): TypedList => $resolvers->add($this->resolver)));
            }
        };

        // withPlugins() and withResolver() both run when called, so no build is needed
        (new Builder())
            ->withPlugins([$plugin($first), $plugin($second)])
            ->withResolver(function (Resolver $resolver) use ($first, $second): void {
                $resolver->withResolvers(function (TypedList $resolvers) use ($first, $second): void {
                    $added = array_values(array_filter(iterator_to_array($resolvers, false), static fn (object $resolver): bool => $resolver === $first || $resolver === $second));
                    $this->assertSame([$first, $second], $added);
                });
            });
    }

    /**
     * A hook added to the builder reaches plugins only once `Hooks` has it too.
     */
    public function testHooksMirrorTheBuildersHooks(): void
    {
        $hooks = static function (\ReflectionClass $class): array {
            $names = array_map(static fn (\ReflectionMethod $method): string => $method->getName(), $class->getMethods(\ReflectionMethod::IS_PUBLIC));
            $names = array_values(array_filter($names, static fn (string $name): bool => str_starts_with($name, 'with')));
            sort($names);

            return $names;
        };

        // the classic Generator hook is deprecated, and plugins do not add plugins
        $this->assertSame(array_values(array_diff($hooks(new \ReflectionClass(Builder::class)), ['withGenerator', 'withPlugins'])), $hooks(new \ReflectionClass(Hooks::class)));
    }

    public function testAPluginGetsTheBuildersLogger(): void
    {
        $plugin = new class () implements PluginInterface, LoggerAwareInterface {
            use LoggerAwareTrait;

            public function configure(Hooks $hooks, array $config): void
            {
                $this->logger?->warning('optional package missing');
            }
        };
        $logger = new CollectingLogger();

        (new Builder($logger))->withPlugins([$plugin]);

        $this->assertSame([['level' => 'warning', 'message' => 'optional package missing']], $logger->entries());
    }

    public function testWhatAPluginLogsIsPartOfTheNextBuild(): void
    {
        $plugin = new class () implements PluginInterface, LoggerAwareInterface {
            use LoggerAwareTrait;

            public function configure(Hooks $hooks, array $config): void
            {
                $this->logger?->error('cannot work here');
            }
        };
        $builder = (new Builder())
            ->setMode(Mode::SPEC)
            ->setSources([])
            ->withSpecification(function (Specification $specification): void {
                $specification->info = new OA\Info(title: 'T', version: '1.0');
                $specification->add(new OA\Operation\Get(path: '/pets', responses: [new OA\Response(response: 200, description: 'ok')]));
            })
            ->withPlugins([$plugin]);

        $this->assertFalse($builder->build()->isValid());
        $this->assertTrue($builder->build()->isValid());
    }

    public function testALoggerSetAfterThePluginsGetsWhatTheyLog(): void
    {
        $plugin = new class () implements PluginInterface, LoggerAwareInterface {
            use LoggerAwareTrait;

            public function configure(Hooks $hooks, array $config): void
            {
                $hooks->withSpecification(fn () => $this->logger?->warning('from the build'));
            }
        };
        $logger = new CollectingLogger();

        (new Builder())
            ->setMode(Mode::SPEC)
            ->setSources([])
            ->withPlugins([$plugin])
            ->setLogger($logger)
            ->build();

        $this->assertContains('from the build', array_column($logger->entries(), 'message'));
    }

    /**
     * @return iterable<string, array{array<array-key, mixed>, string}>
     */
    public static function invalidPlugins(): iterable
    {
        yield 'not a plugin' => [[\stdClass::class], 'Plugin "stdClass" does not implement'];
        yield 'not a class' => [['Bogus'], 'Plugin "Bogus" does not exist'];
        yield 'settings not an array' => [[Fixtures\ServerPlugin::class => true], 'has settings that are not an array, bool given'];
        yield 'settings not keyed by name' => [[Fixtures\ServerPlugin::class => ['https://configured.example']], 'has settings that are not keyed by name'];
        yield 'not instantiable' => [[PluginInterface::class], 'cannot be created'];
        yield 'constructor arguments' => [[Fixtures\ArgumentServerPlugin::class], 'cannot be created'];
    }

    /**
     * @param array<array-key, mixed> $plugins
     */
    #[DataProvider('invalidPlugins')]
    public function testInvalidPluginsAreSkipped(array $plugins, string $expected): void
    {
        $logger = new CollectingLogger();

        (new Builder($logger))->withPlugins($plugins);

        $this->assertCount(1, $logger->entries());
        $this->assertSame('error', $logger->entries()[0]['level']);
        $this->assertStringContainsString($expected, $logger->entries()[0]['message']);
    }

    public function testTheValidPluginsOfAListRun(): void
    {
        $specification = new Specification();
        (new Builder())
            ->setMode(Mode::SPEC)
            ->setSources([])
            ->withPlugins(['Bogus', Fixtures\ServerPlugin::class])
            ->withSpecification(function (Specification $assembled) use (&$specification): void {
                $specification = $assembled;
            })
            ->build();

        $this->assertCount(1, $specification->servers);
    }

    public function testAPluginThrowingStopsTheCall(): void
    {
        $plugin = new class () implements PluginInterface {
            public function configure(Hooks $hooks, array $config): void
            {
                throw new OpenApiException('unusable settings');
            }
        };

        $this->expectException(OpenApiException::class);
        $this->expectExceptionMessage('unusable settings');

        (new Builder())->withPlugins([$plugin, Fixtures\ServerPlugin::class]);
    }

    public function testPluginsAreReportedUnusedInClassicMode(): void
    {
        $logger = new CollectingLogger();
        (new Builder($logger))
            ->setMode(Mode::CLASSIC)
            ->setSources([])
            ->withPlugins([Fixtures\ServerPlugin::class])
            ->build();

        $this->assertContains('Plugins configure the spec pipeline and are not used in classic mode, ignored', array_column($logger->entries(), 'message'));
    }

    protected function typeResolverOf(?object $augmenter): mixed
    {
        $this->assertNotNull($augmenter);

        return (new \ReflectionProperty($augmenter, 'typeResolver'))->getValue($augmenter);
    }
}
