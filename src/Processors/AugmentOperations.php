<?php

/**
 * @license Apache 2.0
 */

namespace Swagger\Processors;

use Swagger\Context;
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
            if ($operation->summary === null) {
                $operation->summary = $operation->_context->extractDescription('summary');
            }
            if ($operation->description === null) {
                $operation->description = $operation->_context->extractDescription('description');
            }
        }
    }
}
