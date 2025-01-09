<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi;

use OpenApi\Analysers\AnalyserInterface;
use OpenApi\Analysers\AttributeAnnotationFactory;
use OpenApi\Analysers\DocBlockAnnotationFactory;
use OpenApi\Analysers\ReflectionAnalyser;
use OpenApi\Annotations as OA;
use OpenApi\Loggers\DefaultLogger;
use Psr\Log\LoggerInterface;

/**
 * OpenApi spec generator.
 *
 * Scans PHP source code and generates OpenApi specifications from the found OpenApi annotations.
 *
 * This is an object-oriented alternative to using the now deprecated `\OpenApi\scan()` function and
 * static class properties of the `Analyzer` and `Analysis` classes.
 */
class Generator
{
    /**
     * Allows Annotation classes to know the context of the annotation that is being processed.
     */
    public static ?Context $context = null;

    /** @var string Magic value to differentiate between null and undefined. */
    public const UNDEFINED = '@OA\Generator::UNDEFINED🙈';

    /** @var array<string,string> */
    public const DEFAULT_ALIASES = ['oa' => 'OpenApi\\Annotations'];
    /** @var array<string> */
    public const DEFAULT_NAMESPACES = ['OpenApi\\Annotations\\'];

    /** @var array<string,string> Map of namespace aliases to be supported by doctrine. */
    protected array $aliases;

    /** @var array<string>|null List of annotation namespaces to be autoloaded by doctrine. */
    protected ?array $namespaces;

    protected ?AnalyserInterface $analyser = null;

    /** @var array<string,mixed> */
    protected array $config = [];

    protected ?Pipeline $processorPipeline = null;

    protected ?LoggerInterface $logger = null;

    /**
     * OpenApi version override.
     *
     * If set, it will override the version set in the `OpenApi` annotation.
     *
     * Due to the order of processing any conditional code using this (via `Context::$version`)
     * must come only after the analysis is finished.
     */
    protected ?string $version = null;

    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger;

        $this->setAliases(self::DEFAULT_ALIASES);
        $this->setNamespaces(self::DEFAULT_NAMESPACES);
    }

    public static function isDefault($value): bool
    {
        return $value === Generator::UNDEFINED;
    }

    /**
     * @return array<string>
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

    public function setAliases(array $aliases): Generator
    {
        $this->aliases = $aliases;

        return $this;
    }

    /**
     * @return array<string>|null
     */
    public function getNamespaces(): ?array
    {
        return $this->namespaces;
    }

    public function addNamespace(string $namespace): Generator
    {
        $namespaces = (array) $this->getNamespaces();
        $namespaces[] = $namespace;

        return $this->setNamespaces(array_unique($namespaces));
    }

    public function setNamespaces(?array $namespaces): Generator
    {
        $this->namespaces = $namespaces;

        return $this;
    }

    public function getAnalyser(): AnalyserInterface
    {
        $this->analyser = $this->analyser ?: new ReflectionAnalyser([new AttributeAnnotationFactory(), new DocBlockAnnotationFactory()]);
        $this->analyser->setGenerator($this);

        return $this->analyser;
    }

    public function setAnalyser(?AnalyserInterface $analyser): Generator
    {
        $this->analyser = $analyser;

        return $this;
    }

    public function getDefaultConfig(): array
    {
        return [
            'operationId' => [
                'hash' => true,
            ],
        ];
    }

    public function getConfig(): array
    {
        return $this->config + $this->getDefaultConfig();
    }

    protected function normaliseConfig(array $config): array
    {
        $normalised = [];
        foreach ($config as $key => $value) {
            if (is_numeric($key)) {
                $token = explode('=', $value);
                if (2 == count($token)) {
                    // 'operationId.hash=false'
                    [$key, $value] = $token;
                }
            }

            if (in_array($value, ['true', 'false'])) {
                $value = 'true' == $value;
            }

            if ($isList = ('[]' === substr($key, -2))) {
                $key = substr($key, 0, -2);
            }
            $token = explode('.', $key);
            if (2 == count($token)) {
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
     * Set generator and/or processor config.
     *
     * @param array<string,mixed> $config
     */
    public function setConfig(array $config): Generator
    {
        $this->config = $this->normaliseConfig($config) + $this->config;

        return $this;
    }

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
                new Processors\BuildPaths(),
                new Processors\AugmentParameters(),
                new Processors\AugmentRefs(),
                new Processors\MergeJsonContent(),
                new Processors\MergeXmlContent(),
                new Processors\OperationId(),
                new Processors\CleanUnmerged(),
                new Processors\PathFilter(),
                new Processors\CleanUnusedComponents(),
                new Processors\AugmentTags(),
            ]);
        }

        $config = $this->getConfig();
        $walker = function (callable $pipe) use ($config) {
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
        };

        return $this->processorPipeline->walk($walker);
    }

    public function setProcessorPipeline(?Pipeline $processor): Generator
    {
        $this->processorPipeline = $processor;

        return $this;
    }

    /**
     * Chainable method that allows to modify the processor pipeline.
     *
     * @param callable $with callable with the current processor pipeline passed in
     */
    public function withProcessor(callable $with): Generator
    {
        $with($this->getProcessorPipeline());

        return $this;
    }

    public function getLogger(): ?LoggerInterface
    {
        return $this->logger ?: new DefaultLogger();
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

    public static function scan(iterable $sources, array $options = []): ?OA\OpenApi
    {
        // merge with defaults
        $config = $options + [
                'aliases' => self::DEFAULT_ALIASES,
                'namespaces' => self::DEFAULT_NAMESPACES,
                'analyser' => null,
                'analysis' => null,
                'processor' => null,
                'processors' => null,
                'config' => [],
                'logger' => null,
                'validate' => true,
                'version' => null,
            ];

        $processorPipeline = $config['processor'] ??
            ($config['processors'] ? new Pipeline($config['processors']) : null);

        return (new Generator($config['logger']))
            ->setVersion($config['version'])
            ->setAliases($config['aliases'])
            ->setNamespaces($config['namespaces'])
            ->setAnalyser($config['analyser'])
            ->setProcessorPipeline($processorPipeline)
            ->setConfig($config['config'])
            ->generate($sources, $config['analysis'], $config['validate']);
    }

    /**
     * Run code in the context of this generator.
     *
     * @param callable $callable Callable in the form of
     *                           `function(Generator $generator, Analysis $analysis, Context $context): mixed`
     *
     * @return mixed the result of the `callable`
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
     * @param iterable      $sources  PHP source files to scan.
     *                                Supported sources:
     *                                * string - file / directory name
     *                                * \SplFileInfo
     *                                * \Symfony\Component\Finder\Finder
     * @param null|Analysis $analysis custom analysis instance
     * @param bool          $validate flag to enable/disable validation of the returned spec
     */
    public function generate(iterable $sources, ?Analysis $analysis = null, bool $validate = true): ?OA\OpenApi
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

        if ($analysis->openapi) {
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

    protected function scanSources(iterable $sources, Analysis $analysis, Context $rootContext): void
    {
        $analyser = $this->getAnalyser();

        foreach ($sources as $source) {
            if (is_iterable($source)) {
                $this->scanSources($source, $analysis, $rootContext);
            } else {
                $resolvedSource = $source instanceof \SplFileInfo ? $source->getPathname() : realpath($source);
                if (!$resolvedSource) {
                    $rootContext->logger->warning(sprintf('Skipping invalid source: %s', $source));
                    continue;
                }
                if (is_dir($resolvedSource)) {
                    $this->scanSources(Util::finder($resolvedSource), $analysis, $rootContext);
                } else {
                    $rootContext->logger->debug(sprintf('Analysing source: %s', $resolvedSource));
                    $analysis->addAnalysis($analyser->fromFile($resolvedSource, $rootContext));
                }
            }
        }
    }
}
