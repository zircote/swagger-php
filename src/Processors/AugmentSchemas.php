<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Processors;

use OpenApi\Analysis;
use OpenApi\Annotations\Components;
use OpenApi\Annotations\Property;
use OpenApi\Annotations\Schema;
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
        /** @var Schema[] $schemas */
        $schemas = $analysis->getAnnotationsOfType(Schema::class);

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
        $unmergedProperties = $analysis->unmerged()->getAnnotationsOfType(Property::class);
        foreach ($unmergedProperties as $property) {
            if ($property->_context->nested) {
                continue;
            }

            $schemaContext = $property->_context->with('class') ?: $property->_context->with('interface') ?: $property->_context->with('trait') ?: $property->_context->with('enum');
            if ($schemaContext->annotations) {
                foreach ($schemaContext->annotations as $annotation) {
                    if ($annotation instanceof Schema) {
                        if ($annotation->_context->nested) {
                            // we shouldn't merge property into nested schemas
                            continue;
                        }

                        if (!Generator::isDefault($annotation->allOf)) {
                            $schema = null;
                            foreach ($annotation->allOf as $nestedSchema) {
                                if (!Generator::isDefault($nestedSchema->ref)) {
                                    continue;
                                }

                                $schema = $nestedSchema;
                            }

                            if ($schema === null) {
                                $schema = new Schema([
                                    '_context' => $annotation->_context,
                                    '_aux' => true,
                                ]);
                                $annotation->allOf[] = $schema;
                            }

                            $schema->merge([$property], true);
                            break;
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
                        $schema->ref = Components::ref($typeSchema);
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
                    $allOfPropertiesSchema = new Schema([
                        'properties' => [],
                        '_context' => $schema->_context,
                        '_aux' => true,
                    ]);
                    $schema->allOf[] = $allOfPropertiesSchema;
                }
                $allOfPropertiesSchema->properties = array_merge($allOfPropertiesSchema->properties, $schema->properties);
                $schema->properties = Generator::UNDEFINED;
            }
        }
    }
}
