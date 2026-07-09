<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi;

use OpenApi\Builder\CollectingLogger;
use OpenApi\Builder\Result;
use OpenApi\Utils\SourceScanner;
use OpenApi\Utils\TokenScanner;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Unified entry point for generating OpenAPI specs.
 *
 * Version resolution (spec pipeline):
 *   setVersion() on the builder > #[OpenApi(version: ...)] from source > 3.1.0 fallback
 *
 * Compiler resolution:
 *   withSpec($compiler) explicit instance > auto-resolved from version
 */
class Builder
{
    /** @var list<string|iterable> */
    protected array $sources = [];

    protected string $mode = 'classic';

    protected ?string $version = null;

    protected ?LoggerInterface $logger = null;

    protected bool $useSpec = false;

    protected ?CompilerInterface $compiler = null;

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
     * @param string $mode 'classic' (default) or 'spec'
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

    /**
     * Switch to the spec attribute pipeline instead of the classic annotation/attribute pipeline.
     */
    public function withSpec(?CompilerInterface $compiler = null): static
    {
        $this->useSpec = true;
        $this->compiler = $compiler;

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
            'classic' => $this->doBuild(),
            default => throw new OpenApiException("Unsupported mode '{$this->mode}'"),
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
                if (class_exists($class) || interface_exists($class) || enum_exists($class)) {
                    $assembler->collect(new \ReflectionClass($class));
                }
            }
        }

        $specification = $assembler->getSpecification();
        $version = $this->version ?? $specification->openapi->version ?? '3.1.0';
        $specification->openapi->version = $version;
        $compiler = $this->compiler ?? $this->resolveCompiler($version);

        $diagnostics = $compiler->validate($specification);
        $output = $compiler->compile($specification);

        return Result::fromSpec($files, $output, $diagnostics);
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
     * @return list<string>
     */
    protected function resolveFiles(): array
    {
        $scanner = new SourceScanner($this->getLogger());

        return $scanner->scan($this->sources);
    }
}
