<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors;

use OpenApi\Analysis;
use OpenApi\Generator;

/**
 * Use the parameter->name as keyfield (parameter->parameter) when used as reusable component
 * (openapi->components->parameters).
 */
class AugmentParameters
{
    public function __invoke(Analysis $analysis)
    {
        if (!Generator::isDefault($analysis->openapi->components) && !Generator::isDefault($analysis->openapi->components->parameters)) {
            $keys = [];
            $parametersWithoutKey = [];
            foreach ($analysis->openapi->components->parameters as $parameter) {
                if (!Generator::isDefault($parameter->parameter)) {
                    $keys[$parameter->parameter] = $parameter;
                } else {
                    $parametersWithoutKey[] = $parameter;
                }
            }
            foreach ($parametersWithoutKey as $parameter) {
                if (!Generator::isDefault($parameter->name) && empty($keys[$parameter->name])) {
                    $parameter->parameter = $parameter->name;
                    $keys[$parameter->parameter] = $parameter;
                }
            }
        }
    }
}
