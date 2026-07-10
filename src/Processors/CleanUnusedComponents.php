<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Undefined;

/**
 * Tracks the use of all <code>Components</code> and removed unused schemas.
 */
class CleanUnusedComponents
{
    use Concerns\AnnotationTrait;

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
        if (!$this->enabled || Undefined::isDefault($analysis->openapi->components)) {
            return;
        }

        // allow multiple runs to catch nested dependencies
        for ($ii = 0; $ii < 10; ++$ii) {
            if (!$this->cleanup($analysis)) {
                break;
            }
        }
    }

    protected function collectAnnotationRefs(OA\AbstractAnnotation $annotation, array &$usedRefs, \SplObjectStorage $visited): void
    {
        if ($visited->offsetExists($annotation)) {
            return;
        }
        $visited->offsetSet($annotation);

        if (property_exists($annotation, 'ref') && !Undefined::isDefault($annotation->ref) && $annotation->ref !== null) {
            $usedRefs[$annotation->ref] = true;
        }

        foreach (['allOf', 'anyOf', 'oneOf'] as $sub) {
            if (property_exists($annotation, $sub) && !Undefined::isDefault($annotation->{$sub})) {
                foreach ($annotation->{$sub} as $subElem) {
                    if ($subElem instanceof OA\AbstractAnnotation) {
                        $this->collectAnnotationRefs($subElem, $usedRefs, $visited);
                    }
                }
            }
        }

        foreach (get_object_vars($annotation) as $property => $value) {
            if (in_array($property, $annotation::$_blacklist)) {
                continue;
            }
            if ($value instanceof OA\AbstractAnnotation) {
                $this->collectAnnotationRefs($value, $usedRefs, $visited);
            } elseif (is_array($value)) {
                foreach ($value as $item) {
                    if ($item instanceof OA\AbstractAnnotation) {
                        $this->collectAnnotationRefs($item, $usedRefs, $visited);
                    }
                }
            }
        }
    }

    protected function cleanup(Analysis $analysis): bool
    {
        $usedRefs = [];
        $visited = new \SplObjectStorage();
        foreach ($analysis->annotations as $annotation) {
            $this->collectAnnotationRefs($annotation, $usedRefs, $visited);

            if ($annotation instanceof OA\OpenApi || $annotation instanceof OA\Operation) {
                if (!Undefined::isDefault($annotation->security)) {
                    foreach ($annotation->security as $security) {
                        foreach (array_keys($security) as $securityName) {
                            $usedRefs[OA\Components::COMPONENTS_PREFIX . 'securitySchemes/' . $securityName] = true;
                        }
                    }
                }
            }
        }

        $unusedComponents = [];
        $seen = [];
        foreach (OA\Components::$_nested as $nested) {
            if (2 == count($nested)) {
                [$componentType] = $nested;
                if (isset($seen[$componentType])) {
                    continue;
                }
                $seen[$componentType] = true;
                if (!Undefined::isDefault($analysis->openapi->components->{$componentType})) {
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

            if (is_array($analysis->openapi->components->{$componentType})) {
                unset($analysis->openapi->components->{$componentType}[$ii]);

                if (!$analysis->openapi->components->{$componentType}) {
                    $analysis->openapi->components->{$componentType} = Undefined::UNDEFINED;
                }
            }
        }

        return [] !== $unusedComponents;
    }
}
