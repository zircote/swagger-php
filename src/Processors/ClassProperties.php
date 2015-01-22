<?php

/**
 * @license Apache 2.0
 */

namespace Swagger\Processors;

use Swagger\Annotations\Property;
use Swagger\Annotations\Swagger;
/**
 *
 */
class ClassProperties {

    public function __invoke(Swagger $swagger) {
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
    }

}
