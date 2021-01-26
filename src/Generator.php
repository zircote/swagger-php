<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi;

use OpenApi\Annotations\OpenApi;
use Psr\Log\LoggerInterface;

/**
 * OpenApi spec generator.
 *
 * Scans PHP source code and generates OpenApi specifications from the found OpenApi annotations.
 *
 * This is an object oriented alternative to using the `\OpenApi\scan()` function and static class properties
 * of the `Analyzer` and `Analysis` classes.
 */
class Generator
{
    /** @var string Special value to differentiate between null and undefined. */
    public const UNDEFINED = '@OA\UNDEFINED🙈';

    /** @var StaticAnalyser The configured analyzer. */
    protected $analyser;

    /** @var null|callable[] List of configured processors. */
    protected $processors = null;

    /** @var LoggerInterface The configured logger. */
    protected $logger;

    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger ?: Logger::psrInstance();
    }

    public function getAnalyser(): StaticAnalyser
    {
        return $this->analyser ?: new StaticAnalyser(null, $this->logger);
    }

    public function setAnalyser(StaticAnalyser $analyser): Generator
    {
        $this->analyser = $analyser;

        return $this;
    }

    /**
     * @return callable[]
     */
    public function getProcessors(): array
    {
        return null !== $this->processors ? $this->processors : Analysis::processors($this->logger);
    }

    /**
     * @param null|callable[] $processors
     */
    public function setProcessors(?array $processors): Generator
    {
        $this->processors = $processors;

        return $this;
    }

    /**
     * Scan the given source files.
     *
     * @param iterable $sources filenames (`string`) or \SplFileInfo
     */
    public function scan(iterable $sources, ?Analysis $analysis = null, bool $validate = true): OpenApi
    {
        // preserve originals
        $whitelist = Analyser::$whitelist;
        $defaultImports = Analyser::$defaultImports;

        try {
            $analysis = $analysis ?: new Analysis([], null, $this->logger);

            $this->scanSources($sources, $analysis);

            // post processing
            $analysis->process($this->getProcessors());
        } finally {
            // restore originals
            Analyser::$whitelist = $whitelist;
            Analyser::$defaultImports = $defaultImports;
        }

        // validation
        if ($validate) {
            $analysis->validate();
        }

        return $analysis->openapi;
    }

    public function scanSources(iterable $sources, Analysis $analysis): void
    {
        $analyser = $this->getAnalyser();
        foreach ($sources as $source) {
            if (is_iterable($source)) {
                $this->scanSources($source, $analysis);
            } else {
                $source = $source instanceof \SplFileInfo ? $source->getPathname() : realpath($source);
                if (is_dir($source)) {
                    $this->scanSources(Util::finder($source), $analysis);
                } else {
                    $analysis->addAnalysis($analyser->fromFile($source));
                }
            }
        }
    }
}
