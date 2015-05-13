<?php

/**
 * @license Apache 2.0
 */

namespace Swagger\Processors;

use Swagger\Annotations\Property;
use Swagger\ClassAnnotations;
use Swagger\Utils\DocComment;

/**
 * Sets the property description from the comment summary.
 */
class PropertyDescriptionComment
{
    public function __invoke(ClassAnnotations $annotations)
    {
        foreach ($annotations->propertyAnnotations as $property) {
            foreach ($property->getSwaggerAnnotations() as $annotation) {
                $this->processAnnotation($property->target, $annotation);
            }
        }
    }

    private function processAnnotation(\ReflectionProperty $property, Property $annotation)
    {
        if (! $annotation->description) {
            $annotation->description = (new DocComment($property->getDocComment()))->getSummary();
        }
    }
}
