<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Utils;

use Symfony\Component\Finder\Finder;

/**
 * Custom Symfony `Finder` that understands `swagger-php` CLI options.
 */
class SourceFinder extends Finder
{
    /**
     * @param string|list<string>      $directory
     * @param string|list<string>|null $exclude
     */
    public function __construct(string|array $directory, null|array|string $exclude = null, string $pattern = '*.php')
    {
        parent::__construct();

        $this
            ->sortByName()
            ->files()
            ->followLinks()
            ->name($pattern);

        $directories = (array) $directory;

        foreach ($directories as $path) {
            if (is_file($path)) {
                $this->append([$path]);
            } else {
                $this->in($path);
            }
        }

        foreach ((array) $exclude as $path) {
            $this->notPath($this->getRelativePath($path, $directories));
        }
    }

    /**
     * `$fullPath` relative to the first of `$directories` it lies in, without leading and trailing slashes.
     *
     * The path is relative in Finder's sense, as `exclude()` and `notPath()` expect it. A path in
     * none of the directories is returned as it is, since it may be a relative path already.
     *
     * @param list<string> $directories
     */
    protected function getRelativePath(string $fullPath, array $directories): string
    {
        foreach ($directories as $directory) {
            if (str_starts_with($fullPath, $directory)) {
                $relativePath = substr($fullPath, strlen($directory));

                if ($relativePath !== '' && $relativePath !== '0') {
                    return trim($relativePath, '/');
                }
            }
        }

        return $fullPath;
    }
}
