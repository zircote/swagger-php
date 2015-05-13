<?php

/**
 * @license Apache 2.0
 */

namespace Swagger\Processors;

use Swagger\Annotations\Property;
use Swagger\ClassAnnotations;

/**
 * Sets the property name from the name of the annotated property.
 */
class PropertyName
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
        if (! $annotation->property) {
            $annotation->property = $property->name;
        }
    }
}
