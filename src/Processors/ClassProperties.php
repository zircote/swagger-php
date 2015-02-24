<?php

/**
 * @license Apache 2.0
 */

namespace Swagger\Processors;

use Swagger\Annotations\Property;
use Swagger\Annotations\Swagger;

/**
 * Use the property context to extract useful information and inject that into the annotation.
 */
class ClassProperties {

    public function __invoke(Swagger $swagger) {
        // Merge @SWG\Property() for php properties into the @SWG\Definition of the class.
        foreach ($swagger->_unmerged as $i => $property) {
            if ($property instanceof Property && $property->_context->is('property')) {
                $classAnnotations = $property->_context->with('class')->annotations;
                foreach ($classAnnotations as $annotation) {
                    if ($annotation instanceof \Swagger\Annotations\Definition) {
                        $annotation->merge([$property]);
                        unset($swagger->_unmerged[$i]);
                    }
                }
            }
        }
        // Use the class names for @SWG\Definition()
        foreach ($swagger->definitions as $definition) {
            if ($definition->name === null && $definition->_context->is('class')) {
                $class = explode('\\', $definition->_context->class);
                $definition->name = array_pop($class);
            }
            // Use the property names for @SWG\Property()
            foreach ($definition->properties as $property) {
                if ($property->name === null && $property->_context->property) {
                    $property->name = $property->_context->property;
                }
            }
        }
    }

}
