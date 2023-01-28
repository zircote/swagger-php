<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors\Concerns;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Context;
use OpenApi\Generator;

/**
 * Steps:
 * 1. Determine direct parent / interfaces / traits
 * 2. With each:
 *    - traverse up inheritance tree
 *      - inherit from first with schema; all other with scheme can be ignored
 *      - merge from all without schema
 *        => update all $ref that might reference a property merged.
 */
trait MergePropertiesTrait
{
    protected function inheritFrom(Analysis $analysis, OA\Schema $schema, OA\Schema $from, string $refPath, Context $context): void
    {
        if (Generator::isDefault($schema->allOf)) {
            $schema->allOf = [];
        }
        // merging other properties into allOf is done in the AugmentSchemas processor
        $schema->allOf[] = $refSchema = new OA\Schema([
            'ref' => OA\Components::ref($refPath),
            '_context' => new Context(['generated' => true], $context),
        ]);
        $analysis->addAnnotation($refSchema, $refSchema->_context);
    }

    protected function mergeProperties(OA\Schema $schema, array $from, array &$existing): void
    {
        foreach ($from['properties'] as $context) {
            if (is_iterable($context->annotations)) {
                foreach ($context->annotations as $annotation) {
                    if ($annotation instanceof OA\Property && !in_array($annotation->_context->property, $existing, true)) {
                        $existing[] = $annotation->_context->property;
                        $schema->merge([$annotation], true);
                    }
                }
            }
        }
    }

    protected function mergeMethods(OA\Schema $schema, array $from, array &$existing): void
    {
        foreach ($from['methods'] as $context) {
            if (is_iterable($context->annotations)) {
                foreach ($context->annotations as $annotation) {
                    if ($annotation instanceof OA\Property && !in_array($annotation->_context->property, $existing, true)) {
                        $existing[] = $annotation->_context->property;
                        $schema->merge([$annotation], true);
                    }
                }
            }
        }
    }
}
