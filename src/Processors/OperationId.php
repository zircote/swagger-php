<?php

/**
 * @license Apache 2.0
 */

namespace Swagger\Processors;

use Swagger\Analysis;
use Swagger\Annotations\Operation;

/**
 * Generate the OperationId based on the context of the Swagger comment.
 */
class OperationId
{
    public function __invoke(Analysis $analysis)
    {
        $allOperations = $analysis->getAnnotationsOfType(Operation::class);

        /** @var \Swagger\Annotations\Operation $operation */
        foreach ($allOperations as $operation) {
            if ($operation->operationId !== UNDEFINED) {
                continue;
            }
            $context = $operation->_context;
            if ($context && $context->method) {
                if ($context->class) {
                    if ($context->namespace) {
                        $operation->operationId = $context->namespace . "\\" . $context->class . "::" . $context->method;
                    } else {
                        $operation->operationId = $context->class . "::" . $context->method;
                    }
                } else {
                    $operation->operationId = $context->method;
                }
            }
        }
    }
}
