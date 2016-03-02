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
            $context = $this->splitDescription($operation->_context->extractDescription());

            if (null === $operation->summary && $context['summary']) {
                $operation->summary = $context['summary'];
            }
            if (null === $operation->description && $context['description']) {
                $operation->description = $context['description'];
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
        if (!$description) {
            return ['summary' => '', 'description' => ''];
        }
        $lines = explode("\n", $description, 3);
        if (count($lines) == 1) {
            return ['summary' => $description, 'description' => ''];
        }
        if (count($lines) == 3 && $lines[1] === '') { // Single line summary - blank line - description
            return ['summary' => $lines[0], 'description' => $lines[2]];
        }
        return ['summary' => '', 'description' => $description];
    }
}
