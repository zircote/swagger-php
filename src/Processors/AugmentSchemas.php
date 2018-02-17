<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace Swagger\Processors;

use Swagger\Analysis;
use Swagger\Annotations\Schema;
use Swagger\Annotations\Property;

/**
 * Use the Schema context to extract useful information and inject that into the annotation.
 * Merges properties
 */
class AugmentSchemas
{
    public function __invoke(Analysis $analysis)
    {
        $schemas = $analysis->getAnnotationsOfType(Schema::class);
        // Use the class names for @OAS\Schema()
        foreach ($schemas as $schema) {
            if ($schema->schema === null) {
                if ($schema->_context->is('class')) {
                    $schema->schema = $schema->_context->class;
                } elseif ($schema->_context->is('trait')) {
                    $schema->schema = $schema->_context->trait;
                }
                // if ($schema->type === null) {
                //     $schema->type = 'object';
                // }
            }
        }
        // Merge unmerged @OAS\Property annotations into the @OAS\Schema of the class
        $unmergedProperties = $analysis->unmerged()->getAnnotationsOfType(Property::class);
        foreach ($unmergedProperties as $property) {
            if ($property->_context->nested) {
                continue;
            }
            $schemaContext = $property->_context->with('class') ?: $property->_context->with('trait');
            if ($schemaContext->annotations) {
                foreach ($schemaContext->annotations as $annotation) {
                    if ($annotation instanceof Schema) {
                        $annotation->merge([$property], true);
                        break;
                    }
                }
            }
        }
    }
}
