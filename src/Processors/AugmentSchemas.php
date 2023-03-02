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
class AugmentSchemas implements ProcessorInterface
{
    public function __invoke(Analysis $analysis)
    {
        /** @var OA\Schema[] $schemas */
        $schemas = $analysis->getAnnotationsOfType(OA\Schema::class);

        $this->augmentSchema($schemas);
        $this->mergeUnmergedProperties($analysis);
        $this->augmentType($analysis, $schemas);
        $this->mergeAllOf($analysis, $schemas);
    }

    /**
     * @param array<OA\Schema> $schemas
     */
    protected function augmentSchema(array $schemas): void
    {
        foreach ($schemas as $schema) {
            if (!$schema->isRoot(OA\Schema::class)) {
                continue;
            }
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
    }

    /**
     * Merge unmerged @OA\Property annotations into the @OA\Schema of the class.
     */
    protected function mergeUnmergedProperties(Analysis $analysis): void
    {
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
    }

    /**
     * Set schema type based on various properties.
     *
     * @param array<OA\Schema> $schemas
     */
    protected function augmentType(Analysis $analysis, array $schemas): void
    {
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
    }

    /**
     * Merge schema properties into `allOf` if both exist.
     *
     * @param array<OA\Schema> $schemas
     */
    protected function mergeAllOf(Analysis $analysis, array $schemas): void
    {
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
