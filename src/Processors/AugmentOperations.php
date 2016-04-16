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
            list($contextSummary, $contextDescription) = $this->splitDescription($operation->_context->extractDescription());

            if (null === $operation->summary && $contextSummary) {
                $operation->summary = $contextSummary;
            }
            if (null === $operation->description && $contextDescription) {
                $operation->description = $contextDescription;
            }
        }
    }

    /**
     * @param string $description
     *
     * @return string[]
     */
    private function splitDescription($description)
    {
        if (!$description || false === strpos($description, "\n")) {
            return array($description, '');
        }

        return explode("\n", $description, 2);
    }
}
