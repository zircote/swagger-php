<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi;

use OpenApi\Analysers\AnalyserInterface;
use OpenApi\Analysers\AttributeAnnotationFactory;
use OpenApi\Analysers\DocBlockAnnotationFactory;
use OpenApi\Analysers\DocBlockParser;
use OpenApi\Analysers\ReflectionAnalyser;
use OpenApi\Analysers\TokenAnalyser;
use OpenApi\Annotations\OpenApi;
use OpenApi\Loggers\DefaultLogger;
use OpenApi\Processors\AugmentParameters;
use OpenApi\Processors\AugmentProperties;
use OpenApi\Processors\AugmentSchemas;
use OpenApi\Processors\BuildPaths;
use OpenApi\Processors\CleanUnmerged;
use OpenApi\Processors\DocBlockDescriptions;
use OpenApi\Processors\ExpandClasses;
use OpenApi\Processors\ExpandInterfaces;
use OpenApi\Processors\ExpandTraits;
use OpenApi\Processors\MergeIntoComponents;
use OpenApi\Processors\MergeIntoOpenApi;
use OpenApi\Processors\MergeJsonContent;
use OpenApi\Processors\MergeXmlContent;
use OpenApi\Processors\OperationId;
use Psr\Log\LoggerInterface;

/**
 * OpenApi spec generator.
 *
 * Scans PHP source code and generates OpenApi specifications from the found OpenApi annotations.
 *
 * This is an object oriented alternative to using the now deprecated `\OpenApi\scan()` function and
 * static class properties of the `Analyzer` and `Analysis` classes.
 *
 * The `aliases` property supersedes the `DocBlockParser::$defaultImports`; `namespaces` maps to `DocBlockParser::$whitelist`.
 */
class Generator
{
    /**
     * Allows Annotation classes to know the context of the annotation that is being processed.
     *
     * @var null|Context
     */
    public static $context;

    /** @var string Magic value to differentiate between null and undefined. */
    public const UNDEFINED = '@OA\Generator::UNDEFINED🙈';

    /** @var array Map of namespace aliases to be supported by doctrine. */
    protected $aliases = null;

    /** @var array List of annotation namespaces to be autoloaded by doctrine. */
    protected $namespaces = null;

    /** @var AnalyserInterface The configured analyzer. */
    protected $analyser;

    /** @var null|callable[] List of configured processors. */
    protected $processors = null;

    /** @var null|LoggerInterface PSR logger. */
    protected $logger = null;

    private $configStack;

    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger;

        // kinda config stack to stay BC...
        $this->configStack = new class() {
            private $defaultImports;
            private $whitelist;

            public function push(Generator $generator): void
            {
                // save current state
                $this->defaultImports = DocBlockParser::$defaultImports;
                $this->whitelist = DocBlockParser::$whitelist;

                // update state with generator config
                DocBlockParser::$defaultImports = $generator->getAliases();
                DocBlockParser::$whitelist = $generator->getNamespaces();
            }

            public function pop(): void
            {
                DocBlockParser::$defaultImports = $this->defaultImports;
                DocBlockParser::$whitelist = $this->whitelist;
            }
        };
    }

    public function getAliases(): array
    {
        $aliases = null !== $this->aliases ? $this->aliases : DocBlockParser::$defaultImports;
        $aliases['oa'] = 'OpenApi\\Annotations';

        return $aliases;
    }

    public function setAliases(?array $aliases): Generator
    {
        $this->aliases = $aliases;

        return $this;
    }

    public function getNamespaces(): array
    {
        $namespaces = null !== $this->namespaces ? $this->namespaces : DocBlockParser::$whitelist;
        $namespaces = false !== $namespaces ? $namespaces : [];
        $namespaces[] = 'OpenApi\\Annotations\\';

        return array_unique($namespaces);
    }

    public function addNamespace(string $namespace): Generator
    {
        $namespaces = $this->getNamespaces();
        $namespaces[] = $namespace;
        $this->setNamespaces($namespaces);

        return $this;
    }

    public function setNamespaces(?array $namespaces): Generator
    {
        $this->namespaces = $namespaces;

        return $this;
    }

    public function getAnalyser(): AnalyserInterface
    {
//        return $this->analyser ?: new TokenAnalyser();
        return $this->analyser ?: new ReflectionAnalyser([new DocBlockAnnotationFactory(), new AttributeAnnotationFactory()]);
    }

    public function setAnalyser(?AnalyserInterface $analyser): Generator
    {
        $this->analyser = $analyser;

        return $this;
    }

    /**
     * @return callable[]
     */
    public function getProcessors(): array
    {
        if (null === $this->processors) {
            $this->processors = [
                new DocBlockDescriptions(),
                new MergeIntoOpenApi(),
                new MergeIntoComponents(),
                new ExpandClasses(),
                new ExpandInterfaces(),
                new ExpandTraits(),
                new AugmentSchemas(),
                new AugmentProperties(),
                new BuildPaths(),
                new AugmentParameters(),
                new MergeJsonContent(),
                new MergeXmlContent(),
                new OperationId(),
                new CleanUnmerged(),
            ];
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
        if (!$matcher) {
            $matcher = $matcher ?: function ($other) use ($processor) {
                $otherClass = get_class($other);

                return $processor instanceof $otherClass;
            };
        }

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

    /**
     * Static  wrapper around `Generator::generate()`.
     *
     * @param iterable $sources PHP source files to scan.
     *                          Supported sources:
     *                          * string
     *                          * \SplFileInfo
     *                          * \Symfony\Component\Finder\Finder
     * @param array    $options
     *                          aliases:    null|array                    Defaults to `DocBlockParser::$defaultImports`.
     *                          namespaces: null|array                    Defaults to `DocBlockParser::$whitelist`.
     *                          analyser:   null|StaticAnalyser           Defaults to a new `StaticAnalyser`.
     *                          analysis:   null|Analysis                 Defaults to a new `Analysis`.
     *                          processors: null|array                    Defaults to `Analysis::processors()`.
     *                          logger:     null|\Psr\Log\LoggerInterface If not set logging will use \OpenApi\Logger as before.
     *                          validate:   bool                          Defaults to `true`.
     */
    public static function scan(iterable $sources, array $options = []): ?OpenApi
    {
        // merge with defaults
        $config = $options + [
                'aliases' => null,
                'namespaces' => null,
                'analyser' => null,
                'analysis' => null,
                'processors' => null,
                'logger' => null,
                'validate' => true,
            ];

        return (new Generator($config['logger']))
            ->setAliases($config['aliases'])
            ->setNamespaces($config['namespaces'])
            ->setAnalyser($config['analyser'])
            ->setProcessors($config['processors'])
            ->generate($sources, $config['analysis'], $config['validate']);
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
        $rootContext = new Context(['logger' => $this->getLogger()]);
        $analysis = $analysis ?: new Analysis([], $rootContext);

        $this->configStack->push($this);
        try {
            $this->scanSources($sources, $analysis, $rootContext);

            // post processing
            $analysis->process($this->getProcessors());

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
        $analyser->setGenerator($this);

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
