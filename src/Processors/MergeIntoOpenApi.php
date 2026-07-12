<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Annotations\OpenApi;
use OpenApi\Context;
use OpenApi\Undefined;

/**
 * Merge all <code>@OA\OpenApi</code> annotations into one.
 */
class MergeIntoOpenApi
{
    protected bool $mergeComponents;

    public function __construct(bool $mergeComponents = false)
    {
        $this->mergeComponents = $mergeComponents;
    }

    public function isMergeComponents(): bool
    {
        return $this->mergeComponents;
    }

    /**
     *  If set to <code>true</code> allow multiple `@OA\Components` annotations to be merged.
     */
    public function setMergeComponents(bool $mergeComponents): MergeIntoOpenApi
    {
        $this->mergeComponents = $mergeComponents;

        return $this;
    }

    public function __invoke(Analysis $analysis): void
    {
        // Auto-create the OpenApi annotation.
        if (!$analysis->openapi instanceof OpenApi) {
            $context = new Context(['generated' => true], $analysis->context);
            $analysis->addAnnotation(new OpenApi(['_context' => $context]), $context);
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

            if ($annotation instanceof OpenApi) {
                $paths = $annotation->paths;
                unset($annotation->paths);
                $openapi->mergeProperties($annotation);
                if (!Undefined::isDefault($paths)) {
                    foreach ($paths as $path) {
                        if (Undefined::isDefault($openapi->paths)) {
                            $openapi->paths = [];
                        }
                        $openapi->paths[] = $path;
                    }
                }
            } elseif ($annotation instanceof OA\AbstractAnnotation
                && in_array(OpenApi::class, $annotation::$_parents)
                && false === $annotation->_context->is('nested')) {
                // A top-level annotation.
                $merge[] = $annotation;
            }
        }

        if ($this->isMergeComponents()) {

            // merge Components
            $componentsList = array_filter($merge, static fn (OA\AbstractAnnotation $annotation): bool => $annotation instanceof OA\Components);
            $firstComponents = $openapi->components;

            if ((!Undefined::isDefault($firstComponents) && $componentsList !== []) || count($merge) > 1) {
                if (Undefined::isDefault($firstComponents)) {
                    $firstComponents = array_shift($componentsList);
                }

                foreach ($componentsList as $components) {
                    foreach (OA\Components::$_nested as $nested) {
                        if (2 == count($nested)) {
                            $property = $nested[0];
                            if (!Undefined::isDefault($components->{$property})) {
                                $firstComponents->merge($components->{$property});
                            }
                        }
                    }

                    $analysis->removeAnnotation($components);
                }

                $merge = array_filter($merge, static fn (OA\AbstractAnnotation $annotation): bool => !$annotation instanceof OA\Components);
                $merge[] = $firstComponents;
            }
        }

        $analysis->mergeAnnotations($openapi, $merge, true);
    }
}
