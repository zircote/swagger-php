<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Context;
use OpenApi\Generator;

/**
 * Merge all @OA\OpenApi annotations into one.
 */
class MergeIntoOpenApi
{
    public function __invoke(Analysis $analysis)
    {
        // Auto-create the OpenApi annotation.
        if (!$analysis->openapi) {
            $context = new Context([], $analysis->context);
            $analysis->addAnnotation(new OA\OpenApi(['_context' => $context]), $context);
        }
        $openapi = $analysis->openapi;
        $openapi->_analysis = $analysis;

        // Merge annotations into the target openapi
        $merge = [];
        /** @var OA\AbstractAnnotation $annotation */
        foreach ($analysis->annotations as $annotation) {
            if ($annotation === $openapi) {
                continue;
            }
            if ($annotation instanceof OA\OpenApi) {
                $paths = $annotation->paths;
                unset($annotation->paths);
                $openapi->mergeProperties($annotation);
                if (!Generator::isDefault($paths)) {
                    foreach ($paths as $path) {
                        if (Generator::isDefault($openapi->paths)) {
                            $openapi->paths = [];
                        }
                        $openapi->paths[] = $path;
                    }
                }
            } elseif (
                $annotation instanceof OA\AbstractAnnotation
                && in_array(OA\OpenApi::class, $annotation::$_parents)
                && false === $annotation->_context->is('nested')) {
                // A top level annotation.
                $merge[] = $annotation;
            }
        }
        $openapi->merge($merge, true);
    }
}
