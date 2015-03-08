<?php

/**
 * @license Apache 2.0
 */

namespace Swagger;

use Swagger\Annotations\Swagger;
use Swagger\Processors\ClassProperties;
use Swagger\Processors\MergeSwagger;
use Swagger\Processors\SwaggerPaths;

/**
 * Special value to differentiate between null and undefined.
 */
define('Swagger\UNDEFINED', '{SWAGGER-PHP-UNDEFINED-46EC-07AB32D2-D50C}');
define('Swagger\Annotations\UNDEFINED', UNDEFINED);
define('Swagger\Processors\UNDEFINED', UNDEFINED);

/**
 *
 * @param string|array|Finder $directory
 * @param string|array $exclude
 */
function scan($directory, $exclude = null) {
    $swagger = new Swagger([]);
    $swagger->_context = Context::detect(1);
    // Crawl directory and parse all files
    $swagger->crawl($directory, $exclude);
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
