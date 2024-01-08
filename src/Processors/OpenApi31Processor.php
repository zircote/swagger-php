<?php

declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Generator;

final class OpenApi31Processor implements ProcessorInterface
{
    public function __invoke(Analysis $analysis)
    {
        if ($analysis->openapi->openapi !== OA\OpenApi::VERSION_3_1_0) {
            return;
        }

        $annotations = $analysis->getAnnotationsOfType(OA\AbstractAnnotation::class);

        foreach ($annotations as $annotation) {
            $this->convertNullable($annotation);
        }
    }

    private function convertNullable(OA\AbstractAnnotation $annotation): void
    {
        if (!property_exists($annotation, 'nullable')) {
            return;
        }

        $nullable = $annotation->nullable;
        $annotation->nullable = Generator::UNDEFINED; // Unregister nullable property

        if (true !== $nullable) {
            return;
        }

        if (property_exists($annotation, 'ref') && !Generator::isDefault($annotation->ref)) {
            if (!property_exists($annotation, 'oneOf')) {
                return;
            }

            $annotation->oneOf = [new OA\Schema(['ref' => $annotation->ref]), new OA\Schema(['type' => 'null'])];
            $annotation->ref = Generator::UNDEFINED;

            return;
        }

        if (property_exists($annotation, 'oneOf') && is_array($annotation->oneOf)) {
            $annotation->oneOf[] = new OA\Schema(['type' => 'null']);
        } elseif (property_exists($annotation, 'anyOf') && is_array($annotation->anyOf)) {
            $annotation->anyOf[] = new OA\Schema(['type' => 'null']);
        } elseif (property_exists($annotation, 'allOf') && is_array($annotation->allOf)) {
            $annotation->allOf[] = new OA\Schema(['type' => 'null']);
        } elseif (property_exists($annotation, 'type') && !Generator::isDefault($annotation->type)) {
            $annotation->type = (array) $annotation->type;
            $annotation->type[] = 'null';
        }
    }
}
