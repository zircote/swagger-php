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
     * Turns the given $fullPath into a relative path based on $basePaths, which can either
     * be a single string path, or a list of possible paths. If a list is given, the first
     * matching basePath in the list will be used to compute the relative path. If no
     * relative path could be computed, the original string will be returned because there
     * is always a chance it was a valid relative path to begin with.
     *
     * It should be noted that these are "relative paths" primarily in Finder's sense of them,
     * and conform specifically to what is expected by functions like <code>exclude()</code> and <code>notPath()</code>.
     *
     * In particular, leading and trailing slashes are removed.
     */
    /**
     * @param list<string> $directories
     */
    private function getRelativePath(string $fullPath, array $directories): string
    {
        foreach ($directories as $directory) {
            if (str_starts_with($fullPath, (string) $directory)) {
                $relativePath = substr($fullPath, strlen((string) $directory));

                if ($relativePath !== '' && $relativePath !== '0') {
                    return trim($relativePath, '/');
                }
            }
        }

        return $fullPath;
    }
}
