<?php

/**
 * @license Apache 2.0
 */

namespace Swagger\Processors;

use Swagger\Annotations\Path;
use Swagger\Annotations\Operation;
use Swagger\Annotations\Swagger;
use Swagger\Logger;
use Swagger\Context;

/**
 * Build the swagger->paths using the detected @SWG\Path and @SWG\Operations (like @SWG\Get, @SWG\Post, etc)
 */
class BuildPaths
{

    public function __invoke(Swagger $swagger)
    {
        $paths = [];
        // Merge @SWG\Paths with the same path.
        foreach ($swagger->paths as $annotation) {
            if (empty($annotation->path)) {
                Logger::notice($annotation->identity() . ' is missing required property "path" in ' . $annotation->_context);
            } elseif (isset($paths[$annotation->path])) {
                $paths[$annotation->path]->mergeProperties($annotation);
            } else {
                $paths[$annotation->path] = $annotation;
            }
        }

        // Merge @SWG\Operations into existing @SWG\Paths or create a new one.
        foreach ($swagger->_unmerged as $i => $operation) {
            if ($operation instanceof Operation && $operation->path) {
                if (empty($paths[$operation->path])) {
                    $paths[$operation->path] = new Path([
                        'path' => $operation->path,
                        '_context' => new Context(['generated' => true], $operation->_context)
                    ]);
                }
                $paths[$operation->path]->merge([$operation]);
                unset($swagger->_unmerged[$i]);
            }
        }
        $swagger->paths = array_values($paths);
    }
}
