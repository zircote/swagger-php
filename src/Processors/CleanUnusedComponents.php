<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Collector;
use OpenApi\Generator;

class CleanUnusedComponents
{
    public function __invoke(Analysis $analysis)
    {
        if (Generator::isDefault($analysis->openapi->components)) {
            return;
        }

        $analysis->annotations = (new Collector())->collect($analysis->annotations);

        // allow multiple runs to catch nested dependencies
        for ($ii = 0; $ii < 10; ++$ii) {
            if (!$this->cleanup($analysis)) {
                break;
            }
        }
    }

    protected function cleanup(Analysis $analysis): bool
    {
        $usedRefs = [];
        foreach ($analysis->annotations as $annotation) {
            if (property_exists($annotation, 'ref') && !Generator::isDefault($annotation->ref) && $annotation->ref !== null) {
                $usedRefs[$annotation->ref] = $annotation->ref;
            }
            foreach (['allOf', 'anyOf', 'oneOff'] as $sub) {
                if (property_exists($annotation, $sub) && !Generator::isDefault($annotation->{$sub})) {
                    foreach ($annotation->{$sub} as $subElem) {
                        if (is_object($subElem) && property_exists($subElem, 'ref') && !Generator::isDefault($subElem->ref) && $subElem->ref !== null) {
                            $usedRefs[$subElem->ref] = $subElem->ref;
                        }
                    }
                }
            }
            if ($annotation instanceof OA\OpenApi || $annotation instanceof OA\Operation) {
                if (!Generator::isDefault($annotation->security)) {
                    foreach ($annotation->security as $security) {
                        foreach (array_keys($security) as $securityName) {
                            $ref = OA\Components::COMPONENTS_PREFIX . 'securitySchemes/' . $securityName;
                            $usedRefs[$ref] = $ref;
                        }
                    }
                }
            }
        }

        $unusedRefs = [];
        foreach (OA\Components::$_nested as $nested) {
            if (2 == count($nested)) {
                // $nested[1] is the name of the property that holds the component name
                [$componentType, $nameProperty] = $nested;
                if (!Generator::isDefault($analysis->openapi->components->{$componentType})) {
                    foreach ($analysis->openapi->components->{$componentType} as $component) {
                        $ref = OA\Components::ref($component);
                        if (!in_array($ref, $usedRefs)) {
                            $unusedRefs[$ref] = [$ref, $nameProperty];
                        }
                    }
                }
            }
        }

        $detachNested = function (Analysis $analysis, OA\AbstractAnnotation $annotation, callable $detachNested): void {
            foreach ($annotation::$_nested as $nested) {
                $nestedKey = ((array) $nested)[0];
                if (!Generator::isDefault($annotation->{$nestedKey})) {
                    if (is_array($annotation->{$nestedKey})) {
                        foreach ($annotation->{$nestedKey} as $elem) {
                            if ($elem instanceof OA\AbstractAnnotation) {
                                $detachNested($analysis, $elem, $detachNested);
                            }
                        }
                    } elseif ($annotation->{$nestedKey} instanceof OA\AbstractAnnotation) {
                        $analysis->annotations->detach($annotation->{$nestedKey});
                    }
                }
            }
            $analysis->annotations->detach($annotation);
        };

        // remove unused
        foreach ($unusedRefs as $refDetails) {
            [$ref, $nameProperty] = $refDetails;
            [$hash, $components, $componentType, $name] = explode('/', $ref);
            foreach ($analysis->openapi->components->{$componentType} as $ii => $component) {
                if ($component->{$nameProperty} == $name) {
                    $annotation = $analysis->openapi->components->{$componentType}[$ii];
                    $detachNested($analysis, $annotation, $detachNested);
                    unset($analysis->openapi->components->{$componentType}[$ii]);
                }
            }
        }

        return 0 != count($unusedRefs);
    }
}
