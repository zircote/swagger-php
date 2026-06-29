<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors\Concerns;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;

trait AnnotationTrait
{
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
