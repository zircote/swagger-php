<?php declare(strict_types=1);

namespace OpenApi\Processors;

use OpenApi\Analysis;
use OpenApi\Annotations\AbstractAnnotation;
use OpenApi\Annotations\Components;
use OpenApi\Generator;

class CleanUnusedComponents
{
    public function __invoke(Analysis $analysis)
    {
        if (Generator::isDefault($analysis->openapi->components)) {
            return;
        }

        for ($ii=0; $ii < 5; ++$ii) {
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
        }

        $unusedRefs = [];
        foreach (Components::$_nested as $nested) {
            if (2 == count($nested)) {
                [$key, $nameKey] = $nested;
                if (!Generator::isDefault($analysis->openapi->components->{$key})) {
                    foreach ($analysis->openapi->components->{$key} as $component) {
                        $ref = Components::ref($component);
                        if (!in_array($ref, $usedRefs)) {
                            $unusedRefs[$ref] = [$ref, $nameKey];
                        }
                    }
                }
            }
        }

        // remove unused
        foreach ($unusedRefs as $details) {
            [$ref, $nameKey] = $details;
            [$hash, $components, $key, $name] = explode('/', $ref);
            foreach ($analysis->openapi->components->{$key} as $ii => $component) {
                if ($component->{$nameKey} == $name) {
                    $annotation = $analysis->openapi->components->{$key}[$ii];
                    // recursive removal of nested annotations
                    foreach ($annotation::$_nested as $nested) {
                        $nestedKey = ((array) $nested)[0];
                        if (!Generator::isDefault($annotation->{$nestedKey})) {
                            if (is_array($annotation->{$nestedKey})) {
                                foreach ($annotation->{$nestedKey} as $elem) {
                                    if ($elem instanceof AbstractAnnotation) {
                                        $analysis->annotations->detach($elem);
                                    }
                                }
                            } elseif ($annotation->{$key} instanceof AbstractAnnotation) {
                                $analysis->annotations->detach($annotation->{$key});
                            }
                        }
                    }
                    $analysis->annotations->detach($annotation);
                    unset($analysis->openapi->components->{$key}[$ii]);
                }
            }
        }

        return 0 != count($unusedRefs);
    }
}
