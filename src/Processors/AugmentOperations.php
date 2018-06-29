<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors;

use OpenApi\Analysis;
use OpenApi\Annotations\Operation;

/**
 * Use the operation context to extract useful information and inject that into the annotation.
 */
class AugmentOperations
{
    public function __invoke(Analysis $analysis)
    {
        $allOperations = $analysis->getAnnotationsOfType(Operation::class);

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
