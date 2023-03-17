<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Generator;

/**
 * Look at all (direct) traits for a schema and:
 * - merge trait annotations/methods/properties into the schema if the trait does not have a schema itself
 * - inherit from the trait if it has a schema (allOf).
 */
class ExpandTraits implements ProcessorInterface
{
    use Concerns\MergePropertiesTrait;

    public function __invoke(Analysis $analysis)
    {
        /** @var OA\Schema[] $schemas */
        $schemas = $analysis->getAnnotationsOfType(OA\Schema::class, true);

        // do regular trait inheritance / merge
        foreach ($schemas as $schema) {
            if ($schema->_context->is('trait')) {
                $traits = $analysis->getTraitsOfClass($schema->_context->fullyQualifiedName($schema->_context->trait), true);
                $existing = [];
                foreach ($traits as $trait) {
                    $traitSchema = $analysis->getSchemaForSource($trait['context']->fullyQualifiedName($trait['trait']));
                    if ($traitSchema) {
                        $refPath = !Generator::isDefault($traitSchema->schema) ? $traitSchema->schema : $trait['trait'];
                        $this->inheritFrom($analysis, $schema, $traitSchema, $refPath, $trait['context']);
                    } else {
                        $this->mergeMethods($schema, $trait, $existing);
                        $this->mergeProperties($schema, $trait, $existing);
                    }
                }
            }
        }

        foreach ($schemas as $schema) {
            if ($schema->_context->is('class') && !$schema->_context->is('generated')) {
                // look at class traits
                $traits = $analysis->getTraitsOfClass($schema->_context->fullyQualifiedName($schema->_context->class), true);
                $existing = [];
                foreach ($traits as $trait) {
                    $traitSchema = $analysis->getSchemaForSource($trait['context']->fullyQualifiedName($trait['trait']));
                    if ($traitSchema) {
                        $refPath = !Generator::isDefault($traitSchema->schema) ? $traitSchema->schema : $trait['trait'];
                        $this->inheritFrom($analysis, $schema, $traitSchema, $refPath, $trait['context']);
                    } else {
                        $this->mergeMethods($schema, $trait, $existing);
                        $this->mergeProperties($schema, $trait, $existing);
                    }
                }

                // also merge ancestor traits of non schema parents
                $ancestors = $analysis->getSuperClasses($schema->_context->fullyQualifiedName($schema->_context->class));
                $existing = [];
                foreach ($ancestors as $ancestor) {
                    $ancestorSchema = $analysis->getSchemaForSource($ancestor['context']->fullyQualifiedName($ancestor['class']));
                    if ($ancestorSchema) {
                        // stop here as we inherit everything above
                        break;
                    } else {
                        $traits = $analysis->getTraitsOfClass($schema->_context->fullyQualifiedName($ancestor['class']), true);
                        foreach ($traits as $trait) {
                            $this->mergeMethods($schema, $trait, $existing);
                            $this->mergeProperties($schema, $trait, $existing);
                        }
                    }
                }
            }
        }
    }
}
