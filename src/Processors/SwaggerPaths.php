<?php

/**
 * @license Apache 2.0
 */

namespace Swagger\Processors;

use Swagger\Annotations\Path;
use Swagger\Annotations\Swagger;
use Swagger\Logger;
use Swagger\Context;

/**
 * Merge & dedupe @SWG\Path and @SWG\Operations (like @SWG\Get, @SWG\Post, etc) into a tree
 */
class SwaggerPaths {

    public function __invoke(Swagger $swagger) {
        $paths = [];
        $operations = [];
        // Merge @SWG\Paths with the same path.
        foreach ($swagger->paths as $annotation) {
            if (empty($annotation->path)) {
                Logger::notice($annotation->identity() . ' is missing required property "path" in ' . $annotation->_context);
                continue;
            }
            if ($annotation instanceof Path) {
                if (isset($paths[$annotation->path])) {
                    $paths[$annotation->path]->mergeProperties($annotation);
                } else {
                    $paths[$annotation->path] = $annotation;
                }
            } else {
                $operations[] = $annotation;
            }
        }
        // Merge @SWG\Operations into existing @SWG\Paths or create a new one.
        foreach ($operations as $operation) {
            if (empty($paths[$operation->path])) {
                $paths[$operation->path] = new Path(['path' => $operation->path]);
                $paths[$operation->path]->_context = new Context(['generated' => true], $operation->_context);
            }
            $paths[$operation->path]->merge([$operation]);
        }
        $swagger->paths = array_values($paths);
    }

}
