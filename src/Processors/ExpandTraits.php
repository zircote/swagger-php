<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Attributes as OAT;
use OpenApi\Generator;

/**
 * Look at all (direct) traits for a schema and:
 * - merge trait annotations/methods/properties into the schema if the trait does not have a schema itself
 * - inherit from the trait if it has a schema (allOf).
 */
class ExpandTraits
{
    use Concerns\MergeTrait;

    public function __invoke(Analysis $analysis)
    {
        /** @var OA\Schema[] $schemas */
        $schemas = $analysis->getAnnotationsOfType([OA\Schema::class, OAT\Schema::class], true);

        foreach ($schemas as $schema) {
            if ($schema->_context->is('class') || $schema->_context->is('trait')) {
                $source = $schema->_context->class ?: $schema->_context->trait;
                $traits = $analysis->getTraitsOfClass($schema->_context->fullyQualifiedName($source), true);
                $existing = [];
                foreach ($traits as $trait) {
                    $traitSchema = $analysis->getSchemaForSource($trait['context']->fullyQualifiedName($trait['trait']));
                    if ($traitSchema) {
                        $refPath = !Generator::isDefault($traitSchema->schema) ? $traitSchema->schema : $trait['trait'];
                        $this->inheritFrom($analysis, $schema, $traitSchema, $refPath, $trait['context']);
                    } else {
                        if ($schema->_context->is('class')) {
                            $this->mergeAnnotations($schema, $trait, $existing);
                            $this->mergeMethods($schema, $trait, $existing);
                            $this->mergeProperties($schema, $trait, $existing);
                        }
                    }
                }
            }
        }
    }
}
