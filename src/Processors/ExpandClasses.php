<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Generator;

/**
 * Iterate over the chain of ancestors of a schema and:
 * - if the ancestor has a schema
 *   => inherit from the ancestor if it has a schema (allOf) and stop.
 * - else
 *   => merge ancestor properties into the schema.
 */
class ExpandClasses
{
    use Concerns\MergePropertiesTrait;

    public function __invoke(Analysis $analysis)
    {
        /** @var OA\Schema[] $schemas */
        $schemas = $analysis->getAnnotationsOfType(OA\Schema::class, true);

        foreach ($schemas as $schema) {
            if ($schema->_context->is('class')) {
                $ancestors = $analysis->getSuperClasses($schema->_context->fullyQualifiedName($schema->_context->class));
                $existing = [];
                foreach ($ancestors as $ancestor) {
                    $ancestorSchema = $analysis->getSchemaForSource($ancestor['context']->fullyQualifiedName($ancestor['class']));
                    if ($ancestorSchema) {
                        $refPath = Generator::isDefault($ancestorSchema->schema) ? $ancestor['class'] : $ancestorSchema->schema;
                        $this->inheritFrom($analysis, $schema, $ancestorSchema, $refPath, $ancestor['context']);

                        // one ancestor is enough
                        break;
                    } else {
                        $this->mergeMethods($schema, $ancestor, $existing);
                        $this->mergeProperties($schema, $ancestor, $existing);
                    }
                }
            }
        }
    }
}
