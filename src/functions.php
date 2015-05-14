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
    $finder = Util::finder($directory, $exclude);
    foreach ($finder as $file) {
        $swagger->merge($analyser->fromFile($file->getPathname()));
    }
    // Post processing
    Processing::process($swagger);
    // Validation (Generate notices & warnings)
    $swagger->validate();
    return $swagger;
}
