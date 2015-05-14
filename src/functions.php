<?php

/**
 * @license Apache 2.0
 */

namespace Swagger;

use InvalidArgumentException;
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
 * @param string|array|Finder $directory The directory(s) or filename(s)
 * @param null|string|array $exclude The directory(s) or filename(s) to exclude (as absolute or relative paths)
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
 * Build a Symfony Finder object that scans the given $directory.
 *
 * @param string|array|Finder $directory The directory(s) or filename(s)
 * @param null|string|array $exclude The directory(s) or filename(s) to exclude (as absolute or relative paths)
 * @throws InvalidArgumentException
 */
function buildFinder($directory, $exclude = null)
{
    if ($directory instanceof Finder) {
        return $directory;
    } else {
        $finder = new Finder();
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
        throw new InvalidArgumentException('Unexpected $directory value:' . gettype($directory));
    }
    if (!is_null($exclude)) {
        if (is_string($exclude)) {
            $finder->notPath((new Util())->getRelativePath($exclude, $directory));
        } elseif (is_array($exclude)) {
            $util = new Util();
            foreach ($exclude as $path) {
                $finder->notPath($util->getRelativePath($path, $directory));
            }
        } else {
            throw new InvalidArgumentException('Unexpected $exclude value:' . gettype($exclude));
        }
    }
    return $finder;
}
