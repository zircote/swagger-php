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
 * Build the openapi->paths using the detected <code>@OA\PathItem</code> and <code>@OA\Operation</code> (<code>@OA\Get</code>, etc).
 */
class BuildPaths
{
    public function __invoke(Analysis $analysis)
    {
        $paths = [];
        // Merge @OA\PathItems with the same path.
        if (!Generator::isDefault($analysis->openapi->paths)) {
            foreach ($analysis->openapi->paths as $annotation) {
                if (empty($annotation->path)) {
                    $annotation->_context->logger->warning($annotation->identity() . ' is missing required property "path" in ' . $annotation->_context);
                } elseif (isset($paths[$annotation->path])) {
                    $paths[$annotation->path]->mergeProperties($annotation);
                    $analysis->annotations->detach($annotation);
                } else {
                    $paths[$annotation->path] = $annotation;
                }
            }
        }

        /** @var OA\Operation[] $operations */
        $operations = $analysis->unmerged()->getAnnotationsOfType(OA\Operation::class);

        // Merge @OA\Operations into existing @OA\PathItems or create a new one.
        foreach ($operations as $operation) {
            if ($operation->path) {
                if (empty($paths[$operation->path])) {
                    $paths[$operation->path] = $pathItem = new OA\PathItem(
                        [
                            'path' => $operation->path,
                            '_context' => new Context(['generated' => true], $operation->_context),
                        ]
                    );
                    $analysis->addAnnotation($pathItem, $pathItem->_context);
                }
                if ($paths[$operation->path]->merge([$operation])) {
                    $operation->_context->logger->warning('Unable to merge ' . $operation->identity() . ' in ' . $operation->_context);
                }
            }
        }
        if ($paths) {
            $analysis->openapi->paths = array_values($paths);
        }
    }
}
