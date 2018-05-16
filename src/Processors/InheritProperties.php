<?php

/**
 * @license Apache 2.0
 */

namespace Swagger\Processors;

use Swagger\Annotations\Property;
use Swagger\Annotations\Swagger;
use Swagger\Annotations\Definition;
use Swagger\Analysis;
use Traversable;

/**
 * Copy the annotated properties from parent classes;
 */
class InheritProperties
{
    public function __invoke(Analysis $analysis)
    {
        $schemas = $analysis->getAnnotationsOfType('\Swagger\Annotations\Schema');
        foreach ($schemas as $schema) {
            if ($schema->_context->is('class')) {
                $existing = [];
                if (is_array($schema->properties) || $schema->properties instanceof Traversable) {
                    foreach ($schema->properties as $property) {
                        if ($property->property) {
                            $existing[] = $property->property;
                        }
                    }
                }
                
                $className = $schema->_context->fullyQualifiedName($schema->_context->class);
                //Get inherited/exteneded classes to combine properties
                $inheritedClasses = $analysis->getSuperClasses($className);
                //Get Traits to combine properties
                $usedTraits = $analysis->getUsedTraits($className);

                $defintions = array_merge($inheritedClasses, $usedTraits);

                foreach ($defintions as $defintion) {
                    foreach ($defintion['properties'] as $property) {
                        if (is_array($property->annotations) === false && !($property->annotations instanceof Traversable)) {
                            continue;
                        }
                        foreach ($property->annotations as $annotation) {
                            if ($annotation instanceof Property && in_array($annotation->property, $existing) === false) {
                                $existing[] = $annotation->property;
                                $schema->merge([$annotation], true);
                            }
                        }
                    }
                }
            }
        }
    }
}
