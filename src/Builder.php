<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi;

use OpenApi\Builder\Mode;
use OpenApi\Builder\Result;
use OpenApi\Utils\AttributeFactory;
use OpenApi\Utils\CollectingLogger;
use OpenApi\Utils\PipeInterface;
use OpenApi\Utils\SourceScanner;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Unified entry point for generating OpenAPI documents.
 *
 * Mode:
 *   setMode(Builder\Mode::CLASSIC) — annotation/attribute pipeline via Generator (default)
 *   setMode(Builder\Mode::SPEC)    — spec attribute pipeline via Assembler + Compiler
 *   setMode(Builder\Mode::HYBRID)  — reduced classic pipeline → HybridBridge → spec Compiler
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
    protected string|\SplFileInfo|\Reflector|iterable $sources = [];

    protected Mode $mode = Mode::CLASSIC;

    protected ?string $version = null;

    protected ?LoggerInterface $logger = null;

    protected ?CompilerInterface $compiler = null;

    /**
     * @var Utils\Pipeline<Specification>|null
     */
    protected ?Utils\Pipeline $augmenters = null;

    /**
     * @var callable|null
     */
    protected $generatorHook;

    protected ?AttributeFactory $attributeFactory = null;

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

    public function setLogger(LoggerInterface $logger): static
    {
        $this->logger = $logger;

        return $this;
    }

    public function setCompiler(CompilerInterface $compiler): static
    {
        $this->compiler = $compiler;

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
     * @param callable(Utils\Pipeline<Specification>): (Utils\Pipeline<Specification>|void) $hook
     */
    public function withAugmenters(callable $hook): static
    {
        $hook($this->getAugmenters());

        return $this;
    }

    public function getAttributeFactory(): AttributeFactory
    {
        $this->attributeFactory ??= new AttributeFactory();

        return $this->attributeFactory;
    }

    /**
     * @param callable(AttributeFactory): (AttributeFactory|void) $hook
     */
    public function withAttributeFactory(callable $hook): static
    {
        $hook($this->getAttributeFactory());

        return $this;
    }

    /**
     * Hook to configure the underlying Generator.
     *
     * The callable receives a default Generator and may either modify it in-place
     * or return a fully configured instance.
     *
     * @param callable(Generator): (Generator|void) $hook
     */
    public function withGenerator(callable $hook): static
    {
        $this->generatorHook = $hook;

        return $this;
    }

    public function build(): Result
    {
        return match ($this->mode) {
            Mode::CLASSIC => $this->doBuildClassic(),
            default => $this->doBuildSpec($this->mode === Mode::HYBRID),
        };
    }

    protected function getLogger(): LoggerInterface
    {
        $this->logger ??= new NullLogger();

        return $this->logger;
    }

    protected function doBuildClassic(): Result
    {
        $collecting = new CollectingLogger($this->getLogger());
        $generator = new Generator($collecting);

        if ($this->version !== null) {
            $generator->setVersion($this->version);
        }

        if ($this->generatorHook !== null) {
            $generator = ($this->generatorHook)($generator) ?? $generator;
        }

        $sourceScanner = new SourceScanner($this->getLogger());
        $files = $sourceScanner->scan($this->sources);

        $openApi = $generator->generate($files);

        return Result::fromClassic($files, $openApi, $collecting->entries());
    }

    protected function doBuildSpec(bool $hybrid = false): Result
    {
        $attributeFactory = $this->getAttributeFactory();
        $assembler = new Assembler(attributeFactory: $attributeFactory);

        $sourceScanner = new SourceScanner($this->getLogger());
        $sourceScanner->scan($this->sources);

        $tokenScanner = $attributeFactory->getTokenScanner();

        foreach ($sourceScanner->getFiles() as $file) {
            foreach (array_keys($tokenScanner->scanFile($file)) as $class) {
                if (class_exists($class) || interface_exists($class) || enum_exists($class) || trait_exists($class)) {
                    $assembler->collect(new \ReflectionClass($class));
                }
            }
        }

        foreach ($sourceScanner->getReflectors() as $reflector) {
            if ($reflector instanceof \ReflectionClass) {
                $assembler->collect($reflector);
            }
        }

        $specification = $assembler->getSpecification();

        if ($hybrid) {
            $this->doHybridAssemble($specification);
        }

        // share the token scanner cache ...
        $this->getAugmenters()->get(Augmenter\Inheritance::class)
            ?->setAttributeFactory($attributeFactory);

        $this->getAugmenters()->process($specification);

        $version = $this->version ?? $specification->openapi->version ?? '3.1.0';
        $specification->openapi->version = $version;
        $compiler = $this->compiler ?? $this->resolveCompiler($version);

        $diagnostics = $compiler->validate($specification);
        $output = $compiler->compile($specification);

        return Result::fromSpec($sourceScanner->getFiles(), $specification, $output, $diagnostics);
    }

    protected function doHybridAssemble(Specification $specification): void
    {
        $collectingLogger = new CollectingLogger($this->getLogger());
        $generator = new Generator($collectingLogger);

        if ($this->version !== null) {
            $generator->setVersion($this->version);
        }

        $generator->setProcessorPipeline(new Utils\Pipeline([
            new Processors\MergeJsonContent(),
            new Processors\MergeXmlContent(),
        ]));

        if ($this->generatorHook !== null) {
            $generator = ($this->generatorHook)($generator) ?? $generator;
        }

        $analysis = new Analysis([], new Context([
            'version' => $generator->getVersion(),
            'logger' => $collectingLogger,
        ]));
        $generator->generate($this->sources, $analysis, validate: false);

        $bridge = new HybridBridge();
        $bridge->fromAnalysis($analysis, $specification);
    }

    protected function resolveCompiler(string $version): CompilerInterface
    {
        $compilers = [
            new Compiler\OpenApi30Compiler(),
            new Compiler\OpenApi31Compiler(),
            new Compiler\OpenApi32Compiler(),
        ];

        foreach ($compilers as $compiler) {
            if ($compiler->supports($version)) {
                return $compiler;
            }
        }

        throw new OpenApiException("No compiler available for version '{$version}'");
    }

    /**
     * @return list<PipeInterface>
     */
    protected function getDefaultAugmenters(): array
    {
        return [
            new Augmenter\Inheritance(),
            new Augmenter\Names(),
            new Augmenter\Enums(),
            new Augmenter\Shortcuts(),
            new Augmenter\PathItems(),
            new Augmenter\Types(),
            new Augmenter\Refs(),
            new Augmenter\PathFilter(),
            new Augmenter\Cleanup(),
            new Augmenter\MediaTypes(),
            new Augmenter\Docblocks(),
            new Augmenter\OperationIds(),
            new Augmenter\Tags(),
            new Augmenter\EnumDescriptions(),
        ];
    }
}
