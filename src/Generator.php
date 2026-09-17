<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi;

use OpenApi\Analysers\AnalyserInterface;
use OpenApi\Analysers\AttributeAnnotationFactory;
use OpenApi\Analysers\DocBlockAnnotationFactory;
use OpenApi\Analysers\ReflectionAnalyser;
use OpenApi\Annotations\OpenApi;
use OpenApi\Loggers\DefaultLogger;
use OpenApi\Type\TypeInfoTypeResolver;
use OpenApi\Utils\Pipeline;
use OpenApi\Utils\SourceScanner;
use Psr\Log\LoggerInterface;

/**
 * OpenApi spec generator.
 *
 * Scans PHP source code and generates OpenApi specifications from the found OpenApi annotations.
 *
 * @phpstan-import-type BuilderSource from Builder
 */
class Generator
{
    /** @deprecated Use {@see Undefined::UNDEFINED} instead. */
    public const UNDEFINED = Undefined::UNDEFINED;

    /** @var array<string,string> */
    public const DEFAULT_ALIASES = ['oa' => 'OpenApi\\Annotations'];

    /** @var list<string> */
    public const DEFAULT_NAMESPACES = ['OpenApi\\Annotations\\'];

    /**
     * Allows Annotation classes to know the context of the annotation that is being processed.
     */
    public static ?Context $context = null;

    /** @var array<string,string> Map of namespace aliases to be supported by doctrine. */
    protected array $aliases;

    /** @var list<string>|null List of annotation namespaces to be autoloaded by doctrine. */
    protected ?array $namespaces;

    protected ?AnalyserInterface $analyser = null;

    /** @var array<string,mixed> */
    protected array $config = [];

    /** @var Pipeline<Analysis>|null */
    protected ?Pipeline $processorPipeline = null;

    protected ?TypeResolverInterface $typeResolver = null;

    protected ?LoggerInterface $logger = null;

    /**
     * OpenApi version override.
     *
     * If set, it will override the version set in the <code>OpenApi</code> annotation.
     *
     * Due to the order of processing, any conditional code using this (via <code>Context::$version</code>)
     * must come only after the analysis is finished.
     */
    protected ?string $version = null;

    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger;

        $this->setAliases(self::DEFAULT_ALIASES);
        $this->setNamespaces(self::DEFAULT_NAMESPACES);
    }

    /**
     * @deprecated use {@see Undefined::isDefault()} instead
     *
     * @param mixed ...$value
     */
    public static function isDefault(...$value): bool
    {
        return Undefined::isDefault(...$value);
    }

    /**
     * @return array<string, string>
     */
    public function getAliases(): array
    {
        return $this->aliases;
    }

    public function addAlias(string $alias, string $namespace): Generator
    {
        $this->aliases[$alias] = $namespace;

        return $this;
    }

    /**
     * @param array<string, string> $aliases
     */
    public function setAliases(array $aliases): Generator
    {
        $this->aliases = $aliases;

        return $this;
    }

    /**
     * @return list<string>|null
     */
    public function getNamespaces(): ?array
    {
        return $this->namespaces;
    }

    public function addNamespace(string $namespace): Generator
    {
        $namespaces = (array) $this->getNamespaces();
        $namespaces[] = $namespace;

        return $this->setNamespaces(array_values(array_unique($namespaces)));
    }

    /**
     * @param list<string>|null $namespaces
     */
    public function setNamespaces(?array $namespaces): Generator
    {
        $this->namespaces = $namespaces;

        return $this;
    }

    public function getAnalyser(): AnalyserInterface
    {
        $generatorConfig = $this->getConfig()['generator'];
        $this->analyser = $this->analyser ?: new ReflectionAnalyser([
            new AttributeAnnotationFactory($generatorConfig['ignoreOtherAttributes']),
            new DocBlockAnnotationFactory(),
        ]);
        $this->analyser->setGenerator($this);

        return $this->analyser;
    }

    public function setAnalyser(?AnalyserInterface $analyser): Generator
    {
        $this->analyser = $analyser;

        return $this;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function getDefaultConfig(): array
    {
        return [
            'generator' => [
                'ignoreOtherAttributes' => false,
            ],
            'mergeIntoOpenApi' => [
                'mergeComponents' => false,
            ],
            'expandEnums' => [
                'enumNames' => null,
            ],
            'augmentParameters' => [
                'augmentOperationParameters' => true,
            ],
            'pathFilter' => [
                'tags' => [],
                'paths' => [],
            ],
            'cleanUnusedComponents' => [
                'enabled' => false,
            ],
            'augmentTags' => [
                'whitelist' => [],
                'withDescription' => true,
            ],
            'operationId' => [
                'hash' => true,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function getConfig(): array
    {
        return $this->config + $this->getDefaultConfig();
    }

    /**
     * Set generator and/or processor config.
     *
     * @param array<string,mixed> $config
     */
    public function setConfig(array $config): Generator
    {
        /** @var array<string, mixed> $normalised */
        $normalised = $this->normaliseConfig($config);
        $this->config = $normalised + $this->config;

        return $this;
    }

    /**
     * @return Pipeline<Analysis>
     */
    public function getProcessorPipeline(): Pipeline
    {
        if (!$this->processorPipeline instanceof Pipeline) {
            $this->processorPipeline = new Pipeline([
                new Processors\DocBlockDescriptions(),
                new Processors\MergeIntoOpenApi(),
                new Processors\MergeIntoComponents(),
                new Processors\ExpandClasses(),
                new Processors\ExpandInterfaces(),
                new Processors\ExpandTraits(),
                new Processors\ExpandEnums(),
                new Processors\AugmentSchemas(),
                new Processors\AugmentRequestBody(),
                new Processors\AugmentProperties(),
                new Processors\AugmentDiscriminators(),
                new Processors\BuildPaths(),
                new Processors\AugmentParameters(),
                new Processors\AugmentRefs(),
                new Processors\AugmentItems(),
                new Processors\MergeJsonContent(),
                new Processors\MergeXmlContent(),
                new Processors\AugmentMediaType(),
                new Processors\OperationId(),
                new Processors\CleanUnmerged(),
                new Processors\PathFilter(),
                new Processors\CleanUnusedComponents(),
                new Processors\AugmentTags(),
            ]);
        }

        $config = $this->getConfig();
        $walker = function (object $pipe) use ($config): void {
            $rc = new \ReflectionClass($pipe);

            // apply config
            $processorKey = lcfirst($rc->getShortName());
            if (array_key_exists($processorKey, $config)) {
                foreach ($config[$processorKey] as $name => $value) {
                    $setter = 'set' . ucfirst($name);
                    if (method_exists($pipe, $setter)) {
                        $pipe->{$setter}($value);
                    }
                }
            }

            if ($pipe instanceof GeneratorAwareInterface) {
                $pipe->setGenerator($this);
            }
        };

        return $this->processorPipeline->walk($walker);
    }

    /**
     * @param Pipeline<Analysis>|null $processor
     */
    public function setProcessorPipeline(?Pipeline $processor): Generator
    {
        $this->processorPipeline = $processor;

        $walker = function (object $pipe): void {
            if ($pipe instanceof GeneratorAwareInterface) {
                $pipe->setGenerator($this);
            }
        };

        if ($this->processorPipeline instanceof Pipeline) {
            $this->processorPipeline->walk($walker);
        }

        return $this;
    }

    /**
     * Chainable method that allows to modify the processor pipeline.
     *
     * @param callable $with callable with the current processor pipeline passed in
     */
    public function withProcessorPipeline(callable $with): Generator
    {
        $with($this->getProcessorPipeline());

        return $this;
    }

    public function setTypeResolver(?TypeResolverInterface $typeResolver): Generator
    {
        $this->typeResolver = $typeResolver;

        return $this;
    }

    public function getTypeResolver(): TypeResolverInterface
    {
        $this->typeResolver ??= new TypeInfoTypeResolver();

        return $this->typeResolver;
    }

    public function getLogger(): ?LoggerInterface
    {
        $this->logger ??= new DefaultLogger();

        return $this->logger;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function setVersion(?string $version): Generator
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Run code in the context of this generator.
     *
     * @param callable $callable Callable in the form of
     *                           <code>function(Generator $generator, Analysis $analysis, Context $context): mixed</code>
     *
     * @return mixed the result of the <code>callable</code>
     */
    public function withContext(callable $callable)
    {
        $rootContext = new Context([
            'version' => $this->getVersion(),
            'logger' => $this->getLogger(),
        ]);
        $analysis = new Analysis([], $rootContext);

        return $callable($this, $analysis, $rootContext);
    }

    /**
     * Generate OpenAPI spec by scanning the given source files.
     *
     * @param iterable                                        $sources  PHP source files to scan.
     *                                                                  Supported sources:
     *                                                                  * string - file / directory name
     *                                                                  * \SplFileInfo
     *                                                                  * \Symfony\Component\Finder\Finder
     * @param null|Analysis                                   $analysis custom analysis instance
     * @param bool                                            $validate flag to enable/disable validation of the returned spec
     * @param iterable<BuilderSource|iterable<BuilderSource>> $sources
     */
    public function generate(iterable $sources, ?Analysis $analysis = null, bool $validate = true): ?OpenApi
    {
        $rootContext = new Context([
            'version' => $this->getVersion(),
            'logger' => $this->getLogger(),
        ]);

        $analysis = $analysis ?: new Analysis([], $rootContext);
        $analysis->context = $analysis->context ?: $rootContext;

        $this->scanSources($sources, $analysis, $rootContext);

        // post-processing
        $this->getProcessorPipeline()->process($analysis);

        if ($analysis->openapi instanceof OpenApi) {
            // overwrite default/annotated version
            $analysis->openapi->openapi = $this->getVersion() ?: $analysis->openapi->openapi;
            // update context to provide the same to validation/serialisation code
            $rootContext->version = $analysis->openapi->openapi;
        }

        // validation
        if ($validate) {
            $analysis->validate();
        }

        return $analysis->openapi;
    }

    /**
     * @param array<int|string, mixed> $config
     *
     * @return array<int|string, mixed>
     */
    protected function normaliseConfig(array $config): array
    {
        $normalised = [];
        foreach ($config as $key => $value) {
            if (is_numeric($key)) {
                $token = explode('=', (string) $value);
                if (2 === count($token)) {
                    // 'operationId.hash=false'
                    [$key, $value] = $token;
                }
            }

            if (in_array($value, ['true', 'false'])) {
                $value = 'true' == $value;
            }

            if ($isList = (str_ends_with((string) $key, '[]'))) {
                $key = substr((string) $key, 0, -2);
            }
            $token = explode('.', (string) $key);
            if (2 === count($token)) {
                // 'operationId.hash' => false
                // namespaced / processor
                if ($isList) {
                    $normalised[$token[0]][$token[1]][] = $value;
                } else {
                    $normalised[$token[0]][$token[1]] = $value;
                }
            } else {
                if ($isList) {
                    $normalised[$key][] = $value;
                } else {
                    $normalised[$key] = $value;
                }
            }
        }

        return $normalised;
    }

    /**
     * @param iterable<BuilderSource|iterable<BuilderSource>> $sources
     */
    protected function scanSources(iterable $sources, Analysis $analysis, Context $rootContext): void
    {
        $analyser = $this->getAnalyser();
        $scanner = new SourceScanner($rootContext->logger);

        foreach ($scanner->scan($sources) as $file) {
            $rootContext->logger->debug(sprintf('Analysing source: %s', $file));
            $analysis->addAnalysis($analyser->fromFile($file, $rootContext));
        }
    }
}
