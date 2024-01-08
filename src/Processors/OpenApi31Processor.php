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

        /** @var OA\Schema[] $annotations */
        $annotations = $analysis->getAnnotationsOfType(OA\Schema::class);

        foreach ($annotations as $annotation) {
            $this->processNullable($annotation);
            $this->processExclusiveMinimum($annotation);
        }
    }

    private function processNullable(OA\Schema $annotation): void
    {
        $nullable = $annotation->nullable;
        $annotation->nullable = Generator::UNDEFINED; // Unregister nullable property

        if (true !== $nullable) {
            return;
        }

        if (!Generator::isDefault($annotation->ref)) {
            if (!property_exists($annotation, 'oneOf')) {
                return;
            }

            $annotation->oneOf = [new OA\Schema(['ref' => $annotation->ref]), new OA\Schema(['type' => 'null'])];
            $annotation->ref = Generator::UNDEFINED;

            return;
        }

        if (is_array($annotation->oneOf)) {
            $annotation->oneOf[] = new OA\Schema(['type' => 'null']);
        } elseif (is_array($annotation->anyOf)) {
            $annotation->anyOf[] = new OA\Schema(['type' => 'null']);
        } elseif (is_array($annotation->allOf)) {
            $annotation->allOf[] = new OA\Schema(['type' => 'null']);
        } elseif (!Generator::isDefault($annotation->type)) {
            $annotation->type = (array) $annotation->type;
            $annotation->type[] = 'null';
        }
    }

    private function processExclusiveMinimum(OA\Schema $annotation): void
    {
        if (Generator::UNDEFINED === $annotation->minimum || Generator::UNDEFINED === $annotation->exclusiveMinimum) {
            return;
        }

        if (true === $annotation->exclusiveMinimum) {
            $annotation->exclusiveMinimum = $annotation->minimum;
            $annotation->minimum = Generator::UNDEFINED;
        } elseif (false === $annotation->exclusiveMinimum) {
            $annotation->exclusiveMinimum = Generator::UNDEFINED;
        }
    }
}
