<?php

/**
 * @license Apache 2.0
 */

namespace Swagger\Processors;

use Swagger\Annotations\Swagger;

/**
 * Use the parameter->name as keyfield (parameter->parameter) when used in swagger object.
 */
class AugmentParameter {

    public function __invoke(Swagger $swagger) {
        if ($swagger->parameters) {
            $keys = [];
            $parametersWithoutKey = [];
            foreach ($swagger->parameters as $parameter) {
                if ($parameter->parameter) {
                    $keys[$parameter->parameter] = $parameter;
                } else {
                    $parametersWithoutKey[] = $parameter;
                }
            }
            foreach ($parametersWithoutKey as $parameter) {
                if ($parameter->name && empty($keys[$parameter->name])) {
                    $parameter->parameter = $parameter->name;
                    $keys[$parameter->parameter] = $parameter;
                }
            }
        }
    }

}
