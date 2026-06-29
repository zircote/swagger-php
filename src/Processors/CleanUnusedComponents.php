<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Generator;

/**
 * Tracks the use of all <code>Components</code> and removed unused schemas.
 */
class CleanUnusedComponents
{
    protected bool $enabled;

    public function __construct(bool $enabled = false)
    {
        $this->enabled = $enabled;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Enables/disables the <code>CleanUnusedComponents</code> processor.
     */
    public function setEnabled(bool $enabled): CleanUnusedComponents
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function __invoke(Analysis $analysis): void
    {
        if (!$this->enabled || Generator::isDefault($analysis->openapi->components)) {
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
                $usedRefs[$annotation->ref] = true;
            }

            foreach (['allOf', 'anyOf', 'oneOf'] as $sub) {
                if (property_exists($annotation, $sub) && !Generator::isDefault($annotation->{$sub})) {
                    foreach ($annotation->{$sub} as $subElem) {
                        if (is_object($subElem) && property_exists($subElem, 'ref') && !Generator::isDefault($subElem->ref) && $subElem->ref !== null) {
                            $usedRefs[$subElem->ref] = true;
                        }
                    }
                }
            }

            if ($annotation instanceof OA\OpenApi || $annotation instanceof OA\Operation) {
                if (!Generator::isDefault($annotation->security)) {
                    foreach ($annotation->security as $security) {
                        foreach (array_keys($security) as $securityName) {
                            $usedRefs[OA\Components::COMPONENTS_PREFIX . 'securitySchemes/' . $securityName] = true;
                        }
                    }
                }
            }
        }

        $unusedComponents = [];
        foreach (OA\Components::$_nested as $nested) {
            if (2 == count($nested)) {
                [$componentType, $nameProperty] = $nested;
                if (!Generator::isDefault($analysis->openapi->components->{$componentType})) {
                    foreach ($analysis->openapi->components->{$componentType} as $ii => $component) {
                        $ref = OA\Components::ref($component);
                        if (!isset($usedRefs[$ref])) {
                            $unusedComponents[] = [$componentType, $ii, $component];
                        }
                    }
                }
            }
        }

        foreach ($unusedComponents as [$componentType, $ii, $component]) {
            $this->removeAnnotationRecursive($analysis, $component);
            unset($analysis->openapi->components->{$componentType}[$ii]);

            if (!$analysis->openapi->components->{$componentType}) {
                $analysis->openapi->components->{$componentType} = Generator::UNDEFINED;
            }
        }

        return [] !== $unusedComponents;
    }

    /**
     * Recursively remove an annotation and all its nested children from the analysis.
     */
    protected function removeAnnotationRecursive(Analysis $analysis, OA\AbstractAnnotation $annotation): void
    {
        $analysis->removeAnnotation($annotation);

        foreach (get_object_vars($annotation) as $property => $value) {
            if (in_array($property, $annotation::$_blacklist)) {
                continue;
            }
            if (is_array($value)) {
                foreach ($value as $item) {
                    if ($item instanceof OA\AbstractAnnotation) {
                        $this->removeAnnotationRecursive($analysis, $item);
                    }
                }
            } elseif ($value instanceof OA\AbstractAnnotation) {
                $this->removeAnnotationRecursive($analysis, $value);
            }
        }
    }
}
