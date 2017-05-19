<?php

/**
 * @license Apache 2.0
 */

namespace Swagger\Processors;

use Swagger\Annotations\OpenApi;
use Swagger\Analysis;
use Swagger\Context;

/**
 * Merge all @SWG\OpenApi annotations into one.
 */
class MergeIntoOpenApi
{
    public function __invoke(Analysis $analysis)
    {
        // Set the first OpenApi annotation as target.
        $openapi = $analysis->openapi;
        if (!$openapi) {
            foreach ($analysis->annotations as $annotation) {
                if ($annotation instanceof OpenApi) {
                    $openapi = $annotation;
                    $annotation->_context->analysis = $analysis;
                    break;
                }
            }
            if (!$openapi) {
                $openapi = new OpenApi(['_context' => new Context(['analysis' => $analysis])]);
                $analysis->annotations->attach($openapi);
            }
        }
        $analysis->openapi = $openapi;
        $analysis->openapi->_analysis = $analysis;

        // Merge all annotations into the target openapi
        $remaining = [];
        foreach ($analysis->annotations as $annotation) {
            if ($annotation === $openapi) {
                continue;
            }
            if ($annotation instanceof OpenApi) {
                $paths = $annotation->paths;
                $definitions = $annotation->definitions;
                unset($annotation->paths);
                unset($annotation->definitions);
                $openapi->mergeProperties($annotation);
                foreach ($paths as $path) {
                    $openapi->paths[] = $path;
                }
                foreach ($definitions as $definition) {
                    $openapi->definitions[] = $definition;
                }
            } elseif (property_exists($annotation, '_context') && $annotation->_context->is('nested') === false) { // A top level annotation.
                $remaining[] = $annotation;
            }
        }
        $openapi->merge($remaining, true);
    }
}
