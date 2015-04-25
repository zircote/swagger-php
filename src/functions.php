<?php

/**
 * @license Apache 2.0
 */

namespace Swagger;

use Swagger\Annotations\Swagger;
use Swagger\Processors\MergeSwagger;
use Swagger\Processors\BuildPaths;
use Swagger\Processors\ClassProperties;
use Swagger\Processors\InheritProperties;
use Swagger\Processors\AugmentParameter;
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
function scan($directory, $exclude = null) {
    $swagger = new Swagger([
        '_context' => Context::detect(1)
    ]);
    // Crawl directory and parse all files
    $swagger->crawl($directory, $exclude);
    // Post processing
    $processors = [
        new MergeSwagger(),
        new BuildPaths(),
        new ClassProperties(),
        new InheritProperties(),
        new AugmentParameter(),
    ];
    foreach ($processors as $processor) {
        $processor($swagger);
    }
    // Validation (Generate notices & warnings)
    $swagger->validate();
    return $swagger;
}
