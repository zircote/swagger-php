<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors;

use OpenApi\Analysis;
use OpenApi\Annotations\AbstractAnnotation;
use OpenApi\Annotations\Components;
use OpenApi\Annotations\OpenApi;
use OpenApi\Annotations\Operation;
use OpenApi\Annotations\Property;
use OpenApi\Generator;

class CleanUnusedComponents
{
    public function __invoke(Analysis $analysis)
    {
        if (Generator::isDefault($analysis->openapi->components)) {
            return;
        }

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
            if ($annotation instanceof OpenApi || $annotation instanceof Operation) {
                if (!Generator::isDefault($annotation->security)) {
                    foreach ($annotation->security as $security) {
                        foreach (array_keys($security) as $securityName) {
                            $ref = Components::COMPONENTS_PREFIX . 'securitySchemes/' . $securityName;
                            $usedRefs[$ref] = $ref;
                        }
                    }
                }
            }
        }

        $unusedRefs = [];
        foreach (Components::$_nested as $nested) {
            if (2 == count($nested)) {
                // $nested[1] is the name of the property that holds the component name
                [$componentType, $nameProperty] = $nested;
                if (!Generator::isDefault($analysis->openapi->components->{$componentType})) {
                    foreach ($analysis->openapi->components->{$componentType} as $component) {
                        $ref = Components::ref($component);
                        if (!in_array($ref, $usedRefs)) {
                            $unusedRefs[$ref] = [$ref, $nameProperty];
                        }
                    }
                }
            }
        }

        $detachNested = function (Analysis $analysis, AbstractAnnotation $annotation, callable $detachNested): void {
            foreach ($annotation::$_nested as $nested) {
                $nestedKey = ((array) $nested)[0];
                if (!Generator::isDefault($annotation->{$nestedKey})) {
                    if (is_array($annotation->{$nestedKey})) {
                        foreach ($annotation->{$nestedKey} as $elem) {
                            if ($elem instanceof AbstractAnnotation) {
                                $detachNested($analysis, $elem, $detachNested);
                            }
                        }
                    } elseif ($annotation->{$nestedKey} instanceof AbstractAnnotation) {
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
