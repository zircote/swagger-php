<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi;

use OpenApi\Builder\CollectingLogger;
use OpenApi\Builder\Result;
use OpenApi\Utils\SourceScanner;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Unified entry point for generating OpenAPI documents.
 *
 * Mode:
 *   setMode('classic') — annotation/attribute pipeline via Generator (default)
 */
class Builder
{
    /** @var list<string|iterable> */
    protected array $sources = [];

    protected string $mode = 'classic';

    protected ?string $version = null;

    protected ?LoggerInterface $logger = null;

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
     * Available modes:
     *   - 'classic': scans source files for annotations/attributes and assembles
     *                the OpenAPI document via Generator (default)
     *
     * @param string $mode 'classic' (default)
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

    /**
     * @return list<string>
     */
    protected function resolveFiles(): array
    {
        $scanner = new SourceScanner($this->getLogger());

        return $scanner->scan($this->sources);
    }
}
