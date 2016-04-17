<?php

/**
 * @license Apache 2.0
 */

namespace Swagger\Processors;

use Swagger\Analysis;
use Swagger\Annotations\Operation;

/**
 * Use the operation context to extract useful information and inject that into the annotation.
 */
class AugmentOperations
{
    public function __invoke(Analysis $analysis)
    {
        $allOperations = $analysis->getAnnotationsOfType('\Swagger\Annotations\Operation');

        /** @var Operation $operation */
        foreach ($allOperations as $operation) {
            if (null === $operation->summary) {
                $operation->summary = $operation->_context->phpdocSummary();
            }
            if (null === $operation->description) {
                $operation->description = $operation->_context->phpdocDescription();
            }
        }
    }
}
