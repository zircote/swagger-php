<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Utils;

use OpenApi\Builder;
use Psr\Log\LoggerInterface;

/**
 * Resolves mixed source inputs into file paths and reflectors.
 *
 * @phpstan-import-type BuilderSource from Builder
 */
class SourceScanner
{
    /** @var list<string> */
    protected array $files = [];

    /** @var list<\Reflector> */
    protected array $reflectors = [];

    public function __construct(protected LoggerInterface $logger)
    {
    }

    /**
     * Scan sources and return resolved file paths.
     *
     * @param list<BuilderSource|iterable<BuilderSource>> $sources
     *
     * @return list<string> resolved absolute file paths
     */
    public function scan(iterable $sources): array
    {
        $this->files = [];
        $this->reflectors = [];
        $this->collect($sources);

        return $this->files;
    }

    /**
     * Return files collected during the last scan().
     *
     * @return list<string>
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * Return reflectors collected during the last scan().
     *
     * @return list<\Reflector>
     */
    public function getReflectors(): array
    {
        return $this->reflectors;
    }

    protected function collect(iterable $sources): void
    {
        foreach ($sources as $source) {
            if ($source instanceof \Reflector) {
                $this->reflectors[] = $source;
            } elseif (is_iterable($source)) {
                $this->collect($source);
            } else {
                $resolvedSource = $source instanceof \SplFileInfo ? $source->getPathname() : realpath($source);
                if (!$resolvedSource) {
                    $this->logger->warning(sprintf('Skipping invalid source: %s', $source));
                    continue;
                }
                if (is_dir($resolvedSource)) {
                    $this->collect(new SourceFinder($resolvedSource));
                } else {
                    $this->files[] = $resolvedSource;
                }
            }
        }
    }
}
