<?php

/**
 * @license Apache 2.0
 */

namespace Swagger\Processors;

use Swagger\Annotations\Path;
use Swagger\Annotations\Swagger;
use Swagger\Logger;

/**
 *
 */
class SwaggerPaths {

    public function __invoke(Swagger $swagger) {
        $paths = [];
        foreach ($swagger->paths as $annotation) {
            if ($annotation instanceof Path) {
                $paths[] = $annotation;
            } else {
                $path = new Path(['path' => $annotation->path]);
                $path->merge([$annotation]);
                $paths[] =$path;
            }
        }
        $swagger->paths = $paths;
    }

}
