<?php

/**
 * @license Apache 2.0
 */

namespace Swagger;

use Swagger\Annotations\Swagger;
use Swagger\Processors\ClassProperties;
use Swagger\Processors\MergeSwagger;
use Swagger\Processors\SwaggerPaths;
use Symfony\Component\Finder\Finder;

/**
 *
 * @param string|array|Finder $directory
 * @param string|array $exclude
 */
function scan($directory, $exclude = null) {
    $swagger = new Swagger([]);
    // Setup Finder
    $finder = new Finder();
    if ($exclude !== null) {
        $finder->exclude($exclude);
    }
    $finder->files()->in($directory);
    // Parse all files
    $parser = new Parser();
    foreach ($finder as $file) {
        $swagger->merge($parser->parseFile($file->getPathname()));
    }
    // Post processing
    $processors = [
        new MergeSwagger(),
        new SwaggerPaths(),
        new ClassProperties(),
    ];
    foreach ($processors as $processor) {
        $processor($swagger);
    }
    // Validation (Generate notices & warnings)
    $swagger->validate();
    return $swagger;
}
