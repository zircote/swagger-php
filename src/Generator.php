<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi;

use Doctrine\Common\Annotations\AnnotationRegistry;
use OpenApi\Analysers\AnalyserInterface;
use OpenApi\Analysers\AttributeAnnotationFactory;
use OpenApi\Analysers\DocBlockAnnotationFactory;
use OpenApi\Analysers\ReflectionAnalyser;
use OpenApi\Annotations\OpenApi;
use OpenApi\Loggers\DefaultLogger;
use Psr\Log\LoggerInterface;

/**
 * OpenApi spec generator.
 *
 * Scans PHP source code and generates OpenApi specifications from the found OpenApi annotations.
 *
 * This is an object oriented alternative to using the now deprecated `\OpenApi\scan()` function and
 * static class properties of the `Analyzer` and `Analysis` classes.
 */
class Generator
{
    /**
     * Allows Annotation classes to know the context of the annotation that is being processed.
     *
     * @var Context|null
     */
    public static $context;

    /** @var string Magic value to differentiate between null and undefined. */
    public const UNDEFINED = '@OA\Generator::UNDEFINED🙈';

    /** @var array<string,string> */
    public const DEFAULT_ALIASES = ['oa' => 'OpenApi\\Annotations'];
    /** @var array<string> */
    public const DEFAULT_NAMESPACES = ['OpenApi\\Annotations\\'];

    /** @var array<string,string> Map of namespace aliases to be supported by doctrine. */
    protected $aliases;

    /** @var array<string>|null List of annotation namespaces to be autoloaded by doctrine. */
    protected $namespaces;

    /** @var AnalyserInterface|null The configured analyzer. */
    protected $analyser;

    /** @var array<string,mixed> */
    protected $config = [];

    /** @var callable[]|null List of configured processors. */
    protected $processors = null;

    /** @var LoggerInterface|null PSR logger. */
    protected $logger = null;

    /**
     * OpenApi version override.
     *
     * If set, it will override the version set in the `OpenApi` annotation.
     *
     * Due to the order of processing any conditional code using this (via `Context::$version`)
     * must come only after the analysis is finished.
     *
     * @var string|null
     */
    protected $version = null;

    private $configStack;

    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger;

        $this->setAliases(self::DEFAULT_ALIASES);
        $this->setNamespaces(self::DEFAULT_NAMESPACES);

        // kinda config stack to stay BC...
        $this->configStack = new class() {
            protected $generator;

            public function push(Generator $generator): void
            {
                $this->generator = $generator;
                if (class_exists(AnnotationRegistry::class, true)) {
                    // keeping track of &this->generator allows to 'disable' the loader after we are done;
                    // no unload, unfortunately :/
                    $gref = &$this->generator;
                    AnnotationRegistry::registerLoader(
                        function (string $class) use (&$gref): bool {
                            if ($gref) {
                                foreach ($gref->getNamespaces() as $namespace) {
                                    if (strtolower(substr($class, 0, strlen($namespace))) === strtolower($namespace)) {
                                        $loaded = class_exists($class);
                                        if (!$loaded && $namespace === 'OpenApi\\Annotations\\') {
                                            if (in_array(strtolower(substr($class, 20)), ['definition', 'path'])) {
                                                // Detected an 2.x annotation?
                                                throw new \Exception('The annotation @SWG\\' . substr($class, 20) . '() is deprecated. Found in ' . Generator::$context . "\nFor more information read the migration guide: https://github.com/zircote/swagger-php/blob/master/docs/Migrating-to-v3.md");
                                            }
                                        }

                                        return $loaded;
                                    }
                                }
                            }

                            return false;
                        }
                    );
                }
            }

            public function pop(): void
            {
                $this->generator = null;
            }
        };
    }

    public static function isDefault($value): bool
    {
        return $value === Generator::UNDEFINED;
    }

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
        $this->analyser = $this->analyser ?: new ReflectionAnalyser([new DocBlockAnnotationFactory(), new AttributeAnnotationFactory()]);
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

    /**
     * Set generator and/or processor config.
     *
     * @param array<string,mixed> $config
     */
    public function setConfig(array $config): Generator
    {
        $this->config = $config + $this->config;

        return $this;
    }

    /**
     * @return callable[]
     */
    public function getProcessors(): array
    {
        if (null === $this->processors) {
            $this->processors = [
                new Processors\DocBlockDescriptions(),
                new Processors\MergeIntoOpenApi(),
                new Processors\MergeIntoComponents(),
                new Processors\ExpandClasses(),
                new Processors\ExpandInterfaces(),
                new Processors\ExpandTraits(),
                new Processors\ExpandEnums(),
                new Processors\AugmentSchemas(),
                new Processors\AugmentProperties(),
                new Processors\BuildPaths(),
                new Processors\AugmentParameters(),
                new Processors\AugmentRefs(),
                new Processors\MergeJsonContent(),
                new Processors\MergeXmlContent(),
                new Processors\OperationId(),
                new Processors\CleanUnmerged(),
            ];
        }

        $config = $this->getConfig();
        foreach ($this->processors as $processor) {
            $rc = new \ReflectionClass($processor);

            // apply config
            $processorKey = lcfirst($rc->getShortName());
            if (array_key_exists($processorKey, $config)) {
                foreach ($config[$processorKey] as $name => $value) {
                    $setter = 'set' . ucfirst($name);
                    if (method_exists($processor, $setter)) {
                        $processor->{$setter}($value);
                    }
                }
            }
        }

        return $this->processors;
    }

    /**
     * @param null|callable[] $processors
     */
    public function setProcessors(?array $processors): Generator
    {
        $this->processors = $processors;

        return $this;
    }

    public function addProcessor(callable $processor): Generator
    {
        $processors = $this->getProcessors();
        $processors[] = $processor;
        $this->setProcessors($processors);

        return $this;
    }

    public function removeProcessor(callable $processor, bool $silent = false): Generator
    {
        $processors = $this->getProcessors();
        if (false === ($key = array_search($processor, $processors, true))) {
            if ($silent) {
                return $this;
            }
            throw new \InvalidArgumentException('Processor not found');
        }
        unset($processors[$key]);
        $this->setProcessors($processors);

        return $this;
    }

    /**
     * Update/replace an existing processor with a new one.
     *
     * @param callable      $processor The new processor
     * @param null|callable $matcher   Optional matcher callable to identify the processor to replace.
     *                                 If none given, matching is based on the processors class.
     */
    public function updateProcessor(callable $processor, ?callable $matcher = null): Generator
    {
        $matcher = $matcher ?: function ($other) use ($processor): bool {
            $otherClass = get_class($other);

            return $processor instanceof $otherClass;
        };

        $processors = array_map(function ($other) use ($processor, $matcher) {
            return $matcher($other) ? $processor : $other;
        }, $this->getProcessors());
        $this->setProcessors($processors);

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

    public static function scan(iterable $sources, array $options = []): ?OpenApi
    {
        // merge with defaults
        $config = $options + [
                'aliases' => self::DEFAULT_ALIASES,
                'namespaces' => self::DEFAULT_NAMESPACES,
                'analyser' => null,
                'analysis' => null,
                'processors' => null,
                'logger' => null,
                'validate' => true,
                'version' => null,
            ];

        return (new Generator($config['logger']))
            ->setVersion($config['version'])
            ->setAliases($config['aliases'])
            ->setNamespaces($config['namespaces'])
            ->setAnalyser($config['analyser'])
            ->setProcessors($config['processors'])
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

        $this->configStack->push($this);
        try {
            return $callable($this, $analysis, $rootContext);
        } finally {
            $this->configStack->pop();
        }
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
    public function generate(iterable $sources, ?Analysis $analysis = null, bool $validate = true): ?OpenApi
    {
        $rootContext = new Context([
            'version' => $this->getVersion(),
            'logger' => $this->getLogger(),
        ]);
        $analysis = $analysis ?: new Analysis([], $rootContext);

        $this->configStack->push($this);
        try {
            $this->scanSources($sources, $analysis, $rootContext);

            // post processing
            $analysis->process($this->getProcessors());

            if ($analysis->openapi) {
                $analysis->openapi->openapi = $this->version ?: $analysis->openapi->openapi;
                $rootContext->version = $analysis->openapi->openapi;
            }

            // validation
            if ($validate) {
                $analysis->validate();
            }
        } finally {
            $this->configStack->pop();
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
                    $analysis->addAnalysis($analyser->fromFile($resolvedSource, $rootContext));
                }
            }
        }
    }
}
