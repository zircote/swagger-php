<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi;

use OpenApi\Builder\Mode;
use OpenApi\Builder\Result;
use OpenApi\Contracts\AttributeFactoryAwareInterface;
use OpenApi\Contracts\CompilerInterface;
use OpenApi\Contracts\ComponentNamerAwareInterface;
use OpenApi\Contracts\ComponentNamerInterface;
use OpenApi\Contracts\MergerInterface;
use OpenApi\Contracts\PluginInterface;
use OpenApi\Contracts\TypeResolverAwareInterface;
use OpenApi\Loggers\CollectingLogger;
use OpenApi\Utils\AttributeFactory;
use OpenApi\Utils\ClassReflector;
use OpenApi\Utils\PipeInterface;
use OpenApi\Utils\SourceScanner;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Unified entry point for generating OpenAPI documents.
 *
 * Mode:
 *   setMode(Builder\Mode::CLASSIC) — annotation/attribute pipeline via Generator
 *   setMode(Builder\Mode::SPEC)    — spec attribute pipeline via Assembler + Compiler
 *   setMode(Builder\Mode::HYBRID)  — reduced classic pipeline → HybridBridge → spec Compiler (default)
 *
 * Set/add sources to process:
 *   addSource() > can be file based (string, \SplFileInfo) or reflection (\ReflectionClass)
 *
 * Version resolution (spec/hybrid pipeline):
 *   setVersion() > #[OpenApi(version: ...)] from source > '3.1.0' fallback
 *
 * Compiler resolution (spec/hybrid pipeline):
 *   setCompiler() explicit > auto-resolved from version
 *
 * @phpstan-type BuilderSource string|\SplFileInfo|\Reflector
 */
class Builder
{
    /**
     * @var list<BuilderSource|iterable<BuilderSource>>
     */
    protected array $sources = [];

    protected Mode $mode = Mode::HYBRID;

    protected ?string $version = null;

    /** Forwards to the logger the builder is given, and holds what the next build reports. */
    protected ?CollectingLogger $logger = null;

    protected ?CompilerInterface $compiler = null;

    protected ?Resolver $resolver = null;

    protected ?ComponentNamerInterface $componentNamer = null;

    /**
     * @var Utils\Pipeline<Specification>|null
     */
    protected ?Utils\Pipeline $augmenters = null;

    /**
     * @var Utils\TypedList<MergerInterface>|null
     */
    protected ?Utils\TypedList $mergers = null;

    /**
     * @var callable|null
     */
    protected $generatorHook;

    protected ?AttributeFactory $attributeFactory = null;

    protected ?Type\TypeResolver $typeResolver = null;

    /** @var list<callable(Specification): (Specification|void)> */
    protected array $specificationHooks = [];

    /** Whether a plugin configured the builder; classic mode does not use what plugins add. */
    protected bool $hasPlugins = false;

    /**
     * @param LoggerInterface|null $logger receives every message of a build; silent when not given
     */
    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = new CollectingLogger($logger);
    }

    /**
     * @param BuilderSource|iterable<BuilderSource> $source
     */
    public function addSource(string|\SplFileInfo|\Reflector|iterable $source): static
    {
        $this->sources[] = $source;

        return $this;
    }

    /**
     * @param list<BuilderSource|iterable<BuilderSource>> $sources
     */
    public function setSources(array $sources): static
    {
        $this->sources = $sources;

        return $this;
    }

    /**
     * @deprecated since 7.0, removed in 8.0
     */
    public function setMode(string|Mode $mode): static
    {
        $this->mode = $mode instanceof Mode ? $mode : Mode::from($mode);

        return $this;
    }

    public function setVersion(string $version): static
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @deprecated since 7.3, removed in 8.0 - pass the logger to the constructor; what plugins log in
     *             `withPlugins()` before this call does not reach the logger
     */
    public function setLogger(LoggerInterface $logger): static
    {
        $this->getLogger()->setLogger($logger);

        return $this;
    }

    public function setCompiler(CompilerInterface $compiler): static
    {
        $this->compiler = $compiler;

        return $this;
    }

    public function getComponentNamer(): ComponentNamerInterface
    {
        $this->componentNamer ??= new ComponentNamer\ShortName();

        return $this->componentNamer;
    }

    /**
     * Name components that are not named explicitly.
     *
     * Every augmenter that names a component asks this one namer, so a reference to a component
     * and the component's own name cannot disagree. It is handed to each augmenter, resolver and
     * merger implementing `ComponentNamerAwareInterface` when the build runs, and replaces any
     * namer set on it directly.
     */
    public function setComponentNamer(ComponentNamerInterface $componentNamer): static
    {
        $this->componentNamer = $componentNamer;

        return $this;
    }

    /**
     * Read PHP types with this resolver.
     *
     * It is handed to each augmenter, resolver and merger implementing
     * `TypeResolverAwareInterface` when the build runs, and replaces any type resolver set on it
     * directly.
     */
    public function setTypeResolver(Type\TypeResolver $typeResolver): static
    {
        $this->typeResolver = $typeResolver;

        return $this;
    }

    public function getResolver(): Resolver
    {
        $this->resolver ??= new Resolver();

        return $this->resolver;
    }

    /**
     * Configure the resolver via callable.
     *
     * Runs when called, against the resolver the builder holds; repeated calls configure the
     * same instance.
     *
     * @param callable(Resolver): (Resolver|void) $hook
     */
    public function withResolver(callable $hook): static
    {
        $hook($this->getResolver());

        return $this;
    }

    /**
     * @return Utils\Pipeline<Specification>
     */
    public function getAugmenters(): Utils\Pipeline
    {
        $this->augmenters ??= new Utils\Pipeline(
            $this->getDefaultAugmenters(),
            groups: [Augmenter\Group::Resolve, Augmenter\Group::Reduce, Augmenter\Group::Augment],
            defaultGroup: Augmenter\Group::Augment,
            logger: $this->getLogger(),
        );

        return $this->augmenters;
    }

    /**
     * Configure the augmenter pipeline via callable.
     *
     * Runs when called, against the pipeline the builder holds; repeated calls configure the
     * same pipeline.
     *
     * @param callable(Utils\Pipeline<Specification>): (Utils\Pipeline<Specification>|void) $hook
     */
    public function withAugmenters(callable $hook): static
    {
        $hook($this->getAugmenters());

        return $this;
    }

    /**
     * @return Utils\TypedList<MergerInterface>
     */
    public function getMergers(): Utils\TypedList
    {
        $this->mergers ??= new Utils\TypedList($this->getDefaultMergers());

        return $this->mergers;
    }

    /**
     * Configure the merger chain via callable.
     *
     * Mergers decide what makes two attributes the same one and what the survivor is. They are
     * tried in registration order and the first to claim a type handles it, so a merger for one
     * type goes ahead of `Merge\LastWins`, which claims everything and ships last.
     *
     * Runs when called, against the list the builder holds; repeated calls configure the same
     * list.
     *
     * @param callable(Utils\TypedList<MergerInterface>): (Utils\TypedList<MergerInterface>|void) $hook
     */
    public function withMergers(callable $hook): static
    {
        $hook($this->getMergers());

        return $this;
    }

    public function getAttributeFactory(): AttributeFactory
    {
        $this->attributeFactory ??= new AttributeFactory();

        return $this->attributeFactory;
    }

    /**
     * Configure the attribute factory via callable. Translators are registered here.
     *
     * Runs when called, against the factory the builder holds; repeated calls configure the
     * same instance.
     *
     * @param callable(AttributeFactory): (AttributeFactory|void) $hook
     */
    public function withAttributeFactory(callable $hook): static
    {
        $hook($this->getAttributeFactory());

        return $this;
    }

    /**
     * Contribute to the `Specification` after assembly and before resolution.
     *
     * The callable receives the assembled `Specification` and adds attributes to it with
     * `Specification::add()`. A `$ref` in a contribution resolves as one in a scanned attribute
     * does, and every augmenter sees what was contributed. A contribution carrying a reflector
     * is treated as the assembler's own; one without is metadata nothing else reaches.
     *
     * Runs during the build, once the `Specification` exists. Hooks accumulate: every one
     * runs, in registration order. Classic mode assembles no `Specification`, so the hooks
     * are never called there.
     *
     * @param callable(Specification): (Specification|void) $hook
     */
    public function withSpecification(callable $hook): static
    {
        $this->specificationHooks[] = $hook;

        return $this;
    }

    /**
     * Let plugins configure the builder, in the order given.
     *
     * Each entry is a plugin class, a plugin class mapped to its settings, or a plugin instance.
     * A class is created without arguments, so the list can come from configuration:
     *
     * ```php
     * $builder->withPlugins([
     *     IntrospectorPlugin::class,
     *     JsonSchemaPlugin::class => ['groups' => ['read']],
     * ]);
     * ```
     *
     * Each plugin is handed the builder's `Builder\Hooks`, so it adds to the resolver, augmenters,
     * mergers, attribute factory and specification but cannot set a service such as the component
     * namer. Runs when called; a second call runs its plugins after the first call's.
     *
     * An invalid entry is skipped and logged as an error. A plugin throwing from `configure()` stops
     * the call: the builder holds whatever the plugin added before it threw, and is not fit to build.
     *
     * A plugin implementing `LoggerAwareInterface` is given the builder's logger before `configure()`.
     * What it logs there is part of the next build's result.
     *
     * @param array<array-key, string|PluginInterface|array<string, mixed>> $plugins
     */
    public function withPlugins(array $plugins): static
    {
        $hooks = new Builder\Hooks($this);

        foreach ($plugins as $key => $value) {
            [$plugin, $config] = is_string($key) ? [$key, $value] : [$value, []];

            try {
                $plugin = $this->createPlugin($plugin);

                if (!is_array($config)) {
                    throw new OpenApiException(sprintf('The settings of plugin %s must be an array, %s given', $plugin::class, get_debug_type($config)));
                }

                if (array_filter(array_keys($config), is_int(...)) !== []) {
                    throw new OpenApiException(sprintf('The settings of plugin %s must be keyed by name', $plugin::class));
                }
            } catch (OpenApiException $openApiException) {
                $this->getLogger()->error('Skipping plugin: ' . $openApiException->getMessage());

                continue;
            }

            if ($plugin instanceof LoggerAwareInterface) {
                $plugin->setLogger($this->getLogger());
            }

            $plugin->configure($hooks, $config);
            $this->hasPlugins = true;
        }

        return $this;
    }

    /**
     * Hook to configure the underlying Generator.
     *
     * The callable receives a default Generator and may either modify it in-place
     * or return a fully configured instance.
     *
     * Runs during the build, once the Generator exists. A second call replaces the first.
     *
     * @deprecated since 7.0, removed in 8.0
     *
     * @param callable(Generator): (Generator|void) $hook
     */
    public function withGenerator(callable $hook): static
    {
        $this->generatorHook = $hook;

        return $this;
    }

    /**
     * Build the document.
     *
     * A `Builder` builds one document. To build another, configure a new `Builder`: a second
     * call is not prevented, but what it reuses from the first is not part of the contract. Each
     * result holds what was logged since the previous build, including what plugins logged.
     */
    public function build(): Result
    {
        return match ($this->mode) {
            Mode::CLASSIC => $this->doBuildClassic(),
            default => $this->doBuildSpec($this->mode === Mode::HYBRID),
        };
    }

    /**
     * A plugin instance as given, or one created without arguments from its class.
     */
    protected function createPlugin(mixed $plugin): PluginInterface
    {
        if (is_string($plugin) && is_a($plugin, PluginInterface::class, true)) {
            $class = new \ReflectionClass($plugin);
            if (!$class->isInstantiable() || ($class->getConstructor()?->getNumberOfRequiredParameters() ?? 0) > 0) {
                throw new OpenApiException(sprintf('Plugin %s cannot be created; a plugin named by its class needs a constructor without required arguments', $plugin));
            }

            return new $plugin();
        }

        if (is_string($plugin) && !class_exists($plugin) && !interface_exists($plugin)) {
            throw new OpenApiException(sprintf('Plugin %s does not exist', $plugin));
        }

        if (!$plugin instanceof PluginInterface) {
            throw new OpenApiException(sprintf('Plugin %s does not implement %s', is_string($plugin) ? $plugin : get_debug_type($plugin), PluginInterface::class));
        }

        return $plugin;
    }

    /**
     * Hands the builder's services to each member of a list that implements the matching aware
     * interface, built-in or added through `withAugmenters()`, `withResolver()` or
     * `withMergers()`.
     *
     * @template T
     *
     * @param Utils\TypedList<T> $members
     */
    protected function shareServices(Utils\TypedList $members, AttributeFactory $attributeFactory): void
    {
        $componentNamer = $this->getComponentNamer();
        $typeResolver = $this->getTypeResolver();

        foreach ($members as $member) {
            if ($member instanceof AttributeFactoryAwareInterface) {
                $member->setAttributeFactory($attributeFactory);
            }
            if ($member instanceof ComponentNamerAwareInterface) {
                $member->setNamer($componentNamer);
            }
            if ($member instanceof TypeResolverAwareInterface) {
                $member->setTypeResolver($typeResolver);
            }
        }
    }

    /**
     * The type resolver handed to each augmenter, resolver and merger implementing
     * `TypeResolverAwareInterface`: the one set with `setTypeResolver()`, or a default.
     */
    protected function getTypeResolver(): Type\TypeResolver
    {
        $this->typeResolver ??= new Type\TypeResolver();

        return $this->typeResolver;
    }

    /**
     * The logger every part of a build reports to; each build's result takes what it collected since the last.
     */
    protected function getLogger(): CollectingLogger
    {
        // a subclass with a constructor of its own may not call this one
        return $this->logger ??= new CollectingLogger();
    }

    protected function doBuildClassic(): Result
    {
        if ($this->hasPlugins) {
            $this->getLogger()->warning('Plugins configure the spec pipeline and are not used in classic mode');
        }

        $generator = new Generator($this->getLogger());

        if ($this->version !== null) {
            $generator->setVersion($this->version);
        }

        if ($this->generatorHook !== null) {
            $generator = ($this->generatorHook)($generator) ?? $generator;
        }

        $sourceScanner = new SourceScanner($this->getLogger());
        $files = $sourceScanner->scan($this->sources);

        $openApi = $generator->generate($files);

        return Result::fromClassic($files, $openApi, $this->getLogger()->drain());
    }

    /**
     * Creates the empty specification a spec or hybrid build assembles into.
     */
    protected function createSpecification(): Specification
    {
        return new Specification();
    }

    protected function doBuildSpec(bool $hybrid = false): Result
    {
        $attributeFactory = $this->getAttributeFactory();
        $attributeFactory->setLogger($this->getLogger());
        $assembler = new Assembler($this->createSpecification(), $attributeFactory);

        $sourceScanner = new SourceScanner($this->getLogger());
        $sourceScanner->scan($this->sources);

        $tokenScanner = $attributeFactory->getTokenScanner();

        foreach ($sourceScanner->getFiles() as $file) {
            foreach (array_keys($tokenScanner->scanFile($file)) as $class) {
                [$rc, $reason] = ClassReflector::tryReflect($class);
                if (!$rc instanceof \ReflectionClass) {
                    $this->getLogger()->warning($reason === null
                        ? 'Skipping unknown ' . $class
                        : "Skipping unloadable {$class}: {$reason}");

                    continue;
                }

                $this->collectSafely($assembler, $rc);
            }
        }

        foreach ($sourceScanner->getReflectors() as $reflector) {
            if ($reflector instanceof \ReflectionClass) {
                $this->collectSafely($assembler, $reflector);
            } else {
                $this->getLogger()->warning(sprintf(
                    'Skipping %s source %s: only ReflectionClass sources are read',
                    $reflector::class,
                    match (true) {
                        $reflector instanceof \ReflectionMethod, $reflector instanceof \ReflectionProperty => $reflector->class . '::' . $reflector->getName(),
                        method_exists($reflector, 'getName') => $reflector->getName(),
                        default => '',
                    },
                ));
            }
        }

        $specification = $assembler->getSpecification();

        foreach ($this->specificationHooks as $hook) {
            $hook($specification);
        }

        // every producer has run and nothing has read a component name yet; the resolver's
        // index is the first reader
        Specification\ComponentName::normalise($specification, deprecate: true);

        // a type resolver reports from wherever it is read
        $this->getTypeResolver()->setLogger($this->getLogger());
        // before the resolvers, the first to run
        $this->getResolver()->withResolvers(fn (Utils\TypedList $resolvers) => $this->shareServices($resolvers, $attributeFactory));
        $this->shareServices($this->getAugmenters(), $attributeFactory);
        $this->shareServices($this->getMergers(), $attributeFactory);

        $this->getResolver()->setLogger($this->getLogger());
        $this->getResolver()->resolve($assembler);

        if ($hybrid) {
            $this->doHybridAssemble($specification);
            Specification\ComponentName::normalise($specification, deprecate: false);
        }

        $this->getAugmenters()->setLogger($this->getLogger());
        $this->getAugmenters()->process($specification);

        $version = $this->version ?? $specification->openapi->version ?? '3.1.0';
        $specification->openapi->version = $version;
        $compiler = $this->compiler ?? $this->resolveCompiler($version);

        $diagnostics = $compiler->validate($specification);
        $output = $compiler->compile($specification);

        return Result::fromSpec(
            $sourceScanner->getFiles(),
            $specification,
            $output,
            [...$this->getLogger()->drain(), ...$diagnostics],
        );
    }

    /**
     * @param \ReflectionClass<object> $reflector
     */
    protected function collectSafely(Assembler $assembler, \ReflectionClass $reflector): void
    {
        try {
            $assembler->collect($reflector);
        } catch (\Throwable $throwable) {
            $this->getLogger()->warning("Skipping unloadable {$reflector->getName()}: {$throwable->getMessage()}");
        }
    }

    protected function doHybridAssemble(Specification $specification): void
    {
        $generator = new Generator($this->getLogger());

        if ($this->version !== null) {
            $generator->setVersion($this->version);
        }

        $generator->setProcessorPipeline(new Utils\Pipeline([]));

        if ($this->generatorHook !== null) {
            $generator = ($this->generatorHook)($generator) ?? $generator;
        }

        $analysis = new Analysis([], new Context([
            'version' => $generator->getVersion(),
            'logger' => $this->getLogger(),
        ]));
        $generator->generate($this->sources, $analysis, validate: false);

        // the generator scans files only, so a class passed as a reflector is analysed on its own
        $analyser = $generator->getAnalyser();
        $sourceScanner = new SourceScanner(new NullLogger());
        $sourceScanner->scan($this->sources);
        foreach ($sourceScanner->getReflectors() as $reflector) {
            if (!$reflector instanceof \ReflectionClass) {
                continue;
            }

            if ($analyser instanceof Analysers\ReflectionAnalyser) {
                $analyser->fromFqdn($reflector->getName(), $analysis);
            } else {
                $this->getLogger()->warning(sprintf('Skipping %s for classic attributes: %s reads files only', $reflector->getName(), $analyser::class));
            }
        }

        $bridge = new HybridBridge();
        $bridge->fromAnalysis($analysis, $specification);
    }

    protected function resolveCompiler(string $version): CompilerInterface
    {
        // a compiler returns what it logs from `validate()`, so it reports past the collecting logger
        $logger = $this->getLogger()->getDelegate();
        $compilers = [
            new Compiler\OpenApi30Compiler($logger),
            new Compiler\OpenApi31Compiler($logger),
            new Compiler\OpenApi32Compiler($logger),
        ];

        foreach ($compilers as $compiler) {
            if ($compiler->supports($version)) {
                return $compiler;
            }
        }

        throw new OpenApiException("No compiler available for version '{$version}'");
    }

    /**
     * @return list<PipeInterface<Specification>>
     */
    protected function getDefaultAugmenters(): array
    {
        // both runs share one registry, so `withMergers()` reaches them both
        $mergers = $this->getMergers();

        return [
            new Augmenter\TypeAliases(),
            new Augmenter\Inheritance(),
            new Augmenter\OperationTags(),
            new Augmenter\ImpliedSchemas(),
            new Augmenter\Names(),
            new Augmenter\Enums(),
            new Augmenter\Shortcuts(),
            new Augmenter\DefaultMediaType(),
            new Augmenter\SecurityRequirements(),
            new Augmenter\PathItems(),
            new Augmenter\PathParameters(),
            new Augmenter\Types(),
            new Augmenter\Properties(),
            new Augmenter\Refs(),
            new Augmenter\Parameters(),
            new Augmenter\Merge($mergers),
            new Augmenter\PathFilter(),
            new Augmenter\Cleanup(),
            new Augmenter\MediaTypes(),
            new Augmenter\Docblocks(),
            new Augmenter\OperationIdTemplates(),
            new Augmenter\OperationIds(),
            new Augmenter\Tags(),
            new Augmenter\EnumDescriptions(),
            // the same pass again, as the last pipe of all: what a late augmenter added is
            // reduced too, so nothing downstream has to be trusted to keep keys unique
            new Augmenter\Merge($mergers, Augmenter\Group::Augment),
        ];
    }

    /**
     * @return list<MergerInterface>
     */
    protected function getDefaultMergers(): array
    {
        return [
            new Merge\Tags(),
            new Merge\LastWins(),
        ];
    }
}
