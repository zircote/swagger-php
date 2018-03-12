<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace Swagger\Processors;

use Swagger\Annotations\PathItem;
use Swagger\Annotations\Operation;
use Swagger\Logger;
use Swagger\Context;
use Swagger\Analysis;

/**
 * Build the openapi->paths using the detected @OAS\PathItem and @OAS\Operations (like @OAS\Get, @OAS\Post, etc)
 */
class BuildPaths
{
    public function __invoke(Analysis $analysis)
    {
        $paths = [];
        // Merge @OAS\PathItems with the same path.
        foreach ($analysis->openapi->paths as $annotation) {
            if (empty($annotation->path)) {
                Logger::notice($annotation->identity() . ' is missing required property "path" in ' . $annotation->_context);
            } elseif (isset($paths[$annotation->path])) {
                $paths[$annotation->path]->mergeProperties($annotation);
                $analysis->annotations->detach($annotation);
            } else {
                $paths[$annotation->path] = $annotation;
            }
        }

        // Merge @OAS\Operations into existing @OAS\PathItems or create a new one.
        $operations = $analysis->unmerged()->getAnnotationsOfType(Operation::class);
        foreach ($operations as $operation) {
            if ($operation->path) {
                if (empty($paths[$operation->path])) {
                    $paths[$operation->path] = new PathItem([
                        'path' => $operation->path,
                        '_context' => new Context(['generated' => true], $operation->_context)
                    ]);
                    $analysis->annotations->attach($paths[$operation->path]);
                }
                if ($paths[$operation->path]->merge([$operation])) {
                    Logger::notice('Unable to merge '.$operation->identity() .' in '.$operation->_context);
                }
            }
        }
        $analysis->openapi->paths = array_values($paths);
    }
}
