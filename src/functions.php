<?php

/**
 * @license Apache 2.0
 */

namespace Swagger;

use Swagger\Annotations\Swagger;
use Symfony\Component\Finder\Finder;

/**
 * Special value to differentiate between null and undefined.
 */
define('Swagger\UNDEFINED', '{SWAGGER-PHP-UNDEFINED-46EC-07AB32D2-D50C}');
define('Swagger\Annotations\UNDEFINED', UNDEFINED);
define('Swagger\Processors\UNDEFINED', UNDEFINED);

/**
 * Scan the filesystem for swagger annotations and build swagger-documentation.
 *
 * @param string|array|Finder $directory
 * @param string|array $exclude
 * @return Swagger
 */
function scan($directory, $exclude = null)
{
    $analyser = new StaticAnalyser();
    $swagger = new Swagger([
        '_context' => Context::detect(1)
    ]);
    // Crawl directory and parse all files
    $finder = buildFinder($directory, $exclude);
    foreach ($finder as $file) {
        $swagger->merge($analyser->fromFile($file->getPathname()));
    }
    // Post processing
    Processing::process($swagger);
    // Validation (Generate notices & warnings)
    $swagger->validate();
    return $swagger;
}

/**
 * Build a Symfony Finder object that scan the given $directory.
 * @param string|array|Finder $directory The directory(s) or filename(s)
 * @param string|array $exclude
 * @throws Exception
 */
function buildFinder($directory, $exclude)
{
    if (is_object($directory)) {
        return $directory;
    } else {
        $finder = new Finder($directory, $exclude);
    }
    $finder->files();
    if (is_string($directory)) {
        if (is_file($directory)) { // Scan a single file?
            $finder->append([$directory]);
        } else { // Scan a directory
            $finder->in($directory);
        }
    } elseif (is_array($directory)) {
        foreach ($directory as $path) {
            if (is_file($path)) { // Scan a file?
                $finder->append([$path]);
            } else {
                $finder->in($path);
            }
        }
    } else {
        throw new Exception('Unexpected $directory value:' . gettype($directory));
    }
    if ($exclude !== null) {
        $finder->exclude($exclude);
    }
    return $finder;
}
