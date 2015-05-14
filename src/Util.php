<?php

/**
 * @license Apache 2.0
 */

namespace Swagger;

/**
 * Class Util
 * Convenient utility functions that don't neatly fit anywhere else
 *
 * @package Swagger
 */
class Util
{
    /**
     * Turns the given $fullPath into a relative path based on $basePaths, which can either
     * be a single string path, or a list of possible paths. If a list is given, the first
     * matching basePath in the list will be used to compute the relative path. If no
     * relative path could be computed, the original string will be returned because there
     * is always a chance it was a valid relative path to begin with.
     *
     * It should be noted that these are "relative paths" primarily in Finder's sense of them,
     * and conform specifically to what is expected by functions like `exclude()` and `notPath()`.
     * In particular, leading and trailing slashes are removed.
     *
     * @param string $fullPath
     * @param string|array $basePaths
     * @return string
     */
    public function getRelativePath($fullPath, $basePaths)
    {
        $relativePath = null;
        if (is_string($basePaths)) { // just a single path, not an array of possible paths
            $relativePath = $this->removePrefix($fullPath, $basePaths);
        } else { // an array of paths
            foreach ($basePaths as $basePath) {
                $relativePath = $this->removePrefix($fullPath, $basePath);
                if (!empty($relativePath)) {
                    break;
                }
            }
        }
        return !empty($relativePath) ? trim($relativePath, '/') : $fullPath;
    }

    /**
     * Removes a prefix from the start of a string if it exists, or null otherwise.
     *
     * @param string $str
     * @param string $prefix
     * @return null|string
     */
    private function removePrefix($str, $prefix)
    {
        if (substr($str, 0, strlen($prefix)) == $prefix) {
            return substr($str, strlen($prefix));
        }
        return null;
    }
}
