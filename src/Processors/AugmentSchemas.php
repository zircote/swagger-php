<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Context;
use OpenApi\Generator;

/**
 * Use the Schema context to extract useful information and inject that into the annotation.
 *
 * Merges properties.
 */
class AugmentSchemas
{
    public function __invoke(Analysis $analysis)
    {
        /** @var OA\Schema[] $schemas */
        $schemas = $analysis->getAnnotationsOfType(OA\Schema::class);

        // Use the class names for @OA\Schema()
        foreach ($schemas as $schema) {
            if (Generator::isDefault($schema->schema)) {
                if ($schema->_context->is('class')) {
                    $schema->schema = $schema->_context->class;
                } elseif ($schema->_context->is('interface')) {
                    $schema->schema = $schema->_context->interface;
                } elseif ($schema->_context->is('trait')) {
                    $schema->schema = $schema->_context->trait;
                } elseif ($schema->_context->is('enum')) {
                    $schema->schema = $schema->_context->enum;
                }
            }
        }

        // Merge unmerged @OA\Property annotations into the @OA\Schema of the class
        $unmergedProperties = $analysis->unmerged()->getAnnotationsOfType(OA\Property::class);
        foreach ($unmergedProperties as $property) {
            if ($property->_context->nested) {
                continue;
            }

            $schemaContext = $property->_context->with('class')
                    ?: $property->_context->with('interface')
                    ?: $property->_context->with('trait')
                    ?: $property->_context->with('enum');
            if ($schemaContext->annotations) {
                foreach ($schemaContext->annotations as $annotation) {
                    if ($annotation instanceof OA\Schema) {
                        if ($annotation->_context->nested) {
                            // we shouldn't merge property into nested schemas
                            continue;
                        }

                        $annotation->merge([$property], true);
                        break;
                    }
                }
            }
        }

        // set schema type based on various properties
        foreach ($schemas as $schema) {
            if (Generator::isDefault($schema->type)) {
                if (is_array($schema->properties) && count($schema->properties) > 0) {
                    $schema->type = 'object';
                } elseif (is_array($schema->additionalProperties) && count($schema->additionalProperties) > 0) {
                    $schema->type = 'object';
                } elseif (is_array($schema->patternProperties) && count($schema->patternProperties) > 0) {
                    $schema->type = 'object';
                } elseif (is_array($schema->propertyNames) && count($schema->propertyNames) > 0) {
                    $schema->type = 'object';
                }
            } else {
                if ($typeSchema = $analysis->getSchemaForSource($schema->type)) {
                    if (Generator::isDefault($schema->format)) {
                        $schema->ref = OA\Components::ref($typeSchema);
                        $schema->type = Generator::UNDEFINED;
                    }
                }
            }
        }

        // move schema properties into allOf if both exist
        foreach ($schemas as $schema) {
            if (!Generator::isDefault($schema->properties) && !Generator::isDefault($schema->allOf)) {
                $allOfPropertiesSchema = null;
                foreach ($schema->allOf as $allOfSchema) {
                    if (!Generator::isDefault($allOfSchema->properties)) {
                        $allOfPropertiesSchema = $allOfSchema;
                        break;
                    }
                }
                if (!$allOfPropertiesSchema) {
                    $allOfPropertiesSchema = new OA\Schema([
                        'properties' => [],
                        '_context' => new Context(['generated' => true], $schema->_context),
                    ]);
                    $analysis->addAnnotation($allOfPropertiesSchema, $allOfPropertiesSchema->_context);
                    $schema->allOf[] = $allOfPropertiesSchema;
                }
                $allOfPropertiesSchema->properties = array_merge($allOfPropertiesSchema->properties, $schema->properties);
                $schema->properties = Generator::UNDEFINED;
            }
        }
    }
}
