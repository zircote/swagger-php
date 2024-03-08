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
            $this->processReference($annotation);
            $this->processNullable($annotation);
            $this->processExclusiveMinimum($annotation);
            $this->processExclusiveMaximum($annotation);
        }
    }

    private function processReference(OA\Schema $annotation): void
    {
        if (Generator::isDefault($annotation->ref)) {
            return;
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
            if (!Generator::isDefault($annotation->oneOf)) {
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
        if (Generator::isDefault($annotation->minimum) || Generator::isDefault($annotation->exclusiveMinimum)) {
            return;
        }

        if (true === $annotation->exclusiveMinimum) {
            $annotation->exclusiveMinimum = $annotation->minimum;
            $annotation->minimum = Generator::UNDEFINED;
        } elseif (false === $annotation->exclusiveMinimum) {
            $annotation->exclusiveMinimum = Generator::UNDEFINED;
        }
    }

    private function processExclusiveMaximum(OA\Schema $annotation): void
    {
        if (Generator::isDefault($annotation->maximum) || Generator::isDefault($annotation->exclusiveMaximum)) {
            return;
        }

        if (true === $annotation->exclusiveMaximum) {
            $annotation->exclusiveMaximum = $annotation->maximum;
            $annotation->maximum = Generator::UNDEFINED;
        } elseif (false === $annotation->exclusiveMaximum) {
            $annotation->exclusiveMaximum = Generator::UNDEFINED;
        }
    }
}
