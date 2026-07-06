<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Utils;

use Psr\Log\LoggerInterface;

/**
 * Resolves mixed source inputs into a flat list of resolved file paths.
 */
class SourceScanner
{
    public function __construct(protected LoggerInterface $logger)
    {
    }

    /**
     * Scan sources and return resolved file paths.
     *
     * @param iterable $sources file/directory paths, \SplFileInfo, \Symfony\Component\Finder\Finder instances, or nested iterables
     *
     * @return string[] resolved absolute file paths
     */
    public function scan(iterable $sources): array
    {
        $files = [];
        $this->collect($sources, $files);

        return $files;
    }

    protected function collect(iterable $sources, array &$files): void
    {
        foreach ($sources as $source) {
            if (is_iterable($source)) {
                $this->collect($source, $files);
            } else {
                $resolvedSource = $source instanceof \SplFileInfo ? $source->getPathname() : realpath($source);
                if (!$resolvedSource) {
                    $this->logger->warning(sprintf('Skipping invalid source: %s', $source));
                    continue;
                }
                if (is_dir($resolvedSource)) {
                    $this->collect(new SourceFinder($resolvedSource), $files);
                } else {
                    $files[] = $resolvedSource;
                }
            }
        }
    }
}
