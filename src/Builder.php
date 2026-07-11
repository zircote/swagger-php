<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi;

use OpenApi\Builder\CollectingLogger;
use OpenApi\Builder\Result;
use OpenApi\Utils\PipeInterface;
use OpenApi\Utils\SourceScanner;
use OpenApi\Utils\TokenScanner;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Unified entry point for generating OpenAPI specs.
 *
 * Mode:
 *   setMode('classic') — annotation/attribute pipeline via Generator (default)
 *   setMode('spec')    — spec attribute pipeline via Assembler + Compiler
 *   setMode('hybrid')  — classic pipeline → HybridBridge → spec Compiler
 *
 * Version resolution (spec/hybrid pipeline):
 *   setVersion() > #[OpenApi(version: ...)] from source > '3.1.0' fallback
 *
 * Compiler resolution (spec/hybrid pipeline):
 *   setCompiler() explicit > auto-resolved from version
 */
class Builder
{
    /** @var list<string|iterable> */
    protected array $sources = [];

    protected string $mode = 'classic';

    protected ?string $version = null;

    protected ?LoggerInterface $logger = null;

    protected ?CompilerInterface $compiler = null;

    /** @var Utils\Pipeline<Specification>|null */
    protected ?Utils\Pipeline $augmenters = null;

    /** @var callable|null */
    protected $generatorHook;

    public function addSource(string|iterable $source): static
    {
        $this->sources[] = $source;

        return $this;
    }

    /**
     * @param list<string|iterable> $sources
     */
    public function setSources(array $sources): static
    {
        $this->sources = $sources;

        return $this;
    }

    /**
     * Select the processing mode.
     *
     * @param string $mode 'classic' (default), 'spec', or 'hybrid'
     */
    public function setMode(string $mode): static
    {
        $this->mode = $mode;

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

    public function setAugmenters(Utils\Pipeline $augmenters): static
    {
        $this->augmenters = $augmenters;

        return $this;
    }

    /**
     * Configure the augmenter pipeline via callable.
     *
     * @param callable(Utils\Pipeline): void $with
     */
    public function withAugmenters(callable $with): static
    {
        $with($this->getAugmenters());

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
            'spec' => $this->doBuildSpec(),
            'hybrid' => $this->doBuildHybrid(),
            default => $this->doBuild(),
        };
    }

    protected function getLogger(): LoggerInterface
    {
        $this->logger ??= new NullLogger();

        return $this->logger;
    }

    protected function doBuild(): Result
    {
        $collecting = new CollectingLogger($this->getLogger());
        $generator = new Generator($collecting);

        if ($this->version !== null) {
            $generator->setVersion($this->version);
        }

        if ($this->generatorHook !== null) {
            $generator = ($this->generatorHook)($generator) ?? $generator;
        }

        $openApi = $generator->generate($this->sources);

        return Result::fromClassic($this->resolveFiles(), $openApi, $collecting->entries());
    }

    protected function doBuildSpec(): Result
    {
        $files = $this->resolveFiles();
        $tokenScanner = new TokenScanner();
        $assembler = new Assembler();

        foreach ($files as $file) {
            require_once $file;
            foreach (array_keys($tokenScanner->scanFile($file)) as $class) {
                if (class_exists($class) || interface_exists($class) || enum_exists($class) || trait_exists($class)) {
                    $assembler->collect(new \ReflectionClass($class));
                }
            }
        }

        $specification = $assembler->getSpecification();

        $this->getAugmenters()->process($specification);

        $version = $this->version ?? $specification->openapi->version ?? '3.1.0';
        $specification->openapi->version = $version;
        $compiler = $this->compiler ?? $this->resolveCompiler($version);

        $diagnostics = $compiler->validate($specification);
        $output = $compiler->compile($specification);

        return Result::fromSpec($files, $output, $diagnostics);
    }

    protected function doBuildHybrid(): Result
    {
        $collecting = new CollectingLogger($this->getLogger());
        $generator = new Generator($collecting);

        if ($this->version !== null) {
            $generator->setVersion($this->version);
        }

        // Structural processors only — produce a bare tree for the bridge.
        // Augmentation (types, refs, descriptions) is left to the spec compiler.
        $generator->setProcessorPipeline(new Utils\Pipeline([
            new Processors\MergeIntoOpenApi(),
            new Processors\MergeIntoComponents(),
            new Processors\BuildPaths(),
            new Processors\MergeJsonContent(),
            new Processors\MergeXmlContent(),
        ]));

        if ($this->generatorHook !== null) {
            $generator = ($this->generatorHook)($generator) ?? $generator;
        }

        $openApi = $generator->generate($this->sources, validate: false);

        if ($openApi === null) {
            return Result::fromClassic($this->resolveFiles(), null, $collecting->entries());
        }

        $bridge = new HybridBridge();
        $specification = $bridge->convert($openApi);

        $this->getAugmenters()->process($specification);

        $version = $this->version ?? $specification->openapi->version ?? '3.1.0';
        $specification->openapi->version = $version;
        $compiler = $this->compiler ?? $this->resolveCompiler($version);

        $diagnostics = $compiler->validate($specification);
        $output = $compiler->compile($specification);

        return Result::fromSpec($this->resolveFiles(), $output, $diagnostics);
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
            new Augmenter\ExpandHierarchy(),
            new Augmenter\InferNames(),
            new Augmenter\Enums(),
            new Augmenter\Type(),
            new Augmenter\Ref(),
            new Augmenter\PathFilter(),
            new Augmenter\CleanUnused(),
            new Augmenter\MediaType(),
            new Augmenter\Docblock(),
            new Augmenter\OperationId(),
            new Augmenter\Tag(),
        ];
    }

    /**
     * @return list<string>
     */
    protected function resolveFiles(): array
    {
        $scanner = new SourceScanner($this->getLogger());

        return $scanner->scan($this->sources);
    }
}
