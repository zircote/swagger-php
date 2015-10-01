<?php

/**
 * @license Apache 2.0
 */

namespace Swagger\Processors;

use Swagger\Annotations\Property;
use Swagger\Annotations\Swagger;
use Swagger\Annotations\Definition;
use Swagger\Analysis;

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

                if (count($schema->properties)) {
                    foreach ($schema->properties as $property) {
                        if ($property->property) {
                            $existing[] = $property->property;
                        }
                    }
                }

                $classes = $analysis->getSuperClasses($schema->_context->fullyQualifiedName($schema->_context->class));

                foreach ($classes as $class) {
                    foreach ($class['properties'] as $property) {
                        foreach ($property->annotations as $annotation) {
                            if (
                                $annotation instanceof Property &&
                                in_array($annotation->property, $existing) === false
                            ) {
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
