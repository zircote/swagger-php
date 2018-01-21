<?php

/**
 * @license Apache 2.0
 */

namespace Swagger\Processors;

use Swagger\Analysis;

/**
 * Replaces the OperationId based on the context of the Swagger comment.
 */
class DetectOperationId
{
    public function __invoke(Analysis $analysis)
    {
        $allOperations = $analysis->getAnnotationsOfType('\Swagger\Annotations\Operation');

        /** @var \Swagger\Annotations\Operation $operation */
        foreach ($allOperations as $operation) {
            $context = $operation->_context;
            if ($context && $context->namespace && $context->class && $context->method) {
                $operation->operationId = $context->namespace . "\\" . $context->class . "::" . $context->method;
            }
        }
    }
}
