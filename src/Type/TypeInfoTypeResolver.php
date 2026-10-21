<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Type;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Context;
use OpenApi\Undefined;
use Psr\Log\LoggerInterface;

/**
 * @deprecated since 7.0, removed in 8.0
 */
class TypeInfoTypeResolver extends AbstractTypeResolver
{
    protected TypeResolver $resolver;

    public function __construct()
    {
        parent::__construct();
        $this->resolver = new TypeResolver();
    }

    /**
     * @inheritdoc
     *
     * @param class-string<OA\AbstractAnnotation> $sourceClass
     */
    protected function doAugment(Analysis $analysis, OA\Schema $schema, \Reflector $reflector, string $sourceClass = OA\Schema::class): void
    {
        if ($analysis->context?->logger instanceof LoggerInterface) {
            $this->resolver->setLogger($analysis->context->logger);
        }
        $schemaType = $this->resolver->resolve($reflector);

        if (!$schemaType instanceof SchemaType) {
            $this->handlePostAugment($schema);

            return;
        }

        if (Undefined::isDefault($schema->nullable) && $schemaType->nullable === true) {
            $schema->nullable = true;
        }

        if (Undefined::isDefault($schema->type, $schema->oneOf, $schema->allOf, $schema->anyOf)) {
            $this->applyToAnnotation($schema, $schemaType, $analysis, $sourceClass);
        }

        $this->type2ref($schema, $analysis, $sourceClass);

        $this->handlePostAugment($schema);
    }

    protected function handlePostAugment(OA\Schema $schema): void
    {
        if ($schema->items instanceof OA\Items) {
            $schema->type = 'array';
        }

        if (!Undefined::isDefault($schema->const) && Undefined::isDefault($schema->type)) {
            if (!$this->mapNativeType($schema, gettype($schema->const))) {
                $schema->type = Undefined::UNDEFINED;
            }
        }

        if (!Undefined::isDefault($schema->type) && !$this->mapNativeType($schema, $schema->type)) {
            $schema->type = Undefined::UNDEFINED;
        }
    }

    /**
     * @param class-string<OA\AbstractAnnotation> $sourceClass
     */
    protected function applyToAnnotation(OA\Schema $schema, SchemaType $schemaType, Analysis $analysis, string $sourceClass = OA\Schema::class): void
    {
        // a class is referenced only when there is no explicit enum it would replace
        if ($schemaType->type !== null && (!$schemaType->isRef() || Undefined::isDefault($schema->enum))) {
            /** @var non-empty-array<string>|string $type every SchemaType array type is built non-empty */
            $type = $schemaType->type;
            $schema->type = $type;
        }

        if ($schemaType->format !== null && Undefined::isDefault($schema->format)) {
            $schema->format = $schemaType->format;
        }

        if ($schemaType->minimum !== null && Undefined::isDefault($schema->minimum)) {
            $schema->minimum = $schemaType->minimum;
        }

        if ($schemaType->maximum !== null && Undefined::isDefault($schema->maximum)) {
            $schema->maximum = $schemaType->maximum;
        }

        if ($schemaType->not !== null && Undefined::isDefault($schema->not)) {
            $schema->not = $schemaType->not;
        }

        if ($schemaType->items instanceof SchemaType) {
            $schema->type = 'array';
            if (Undefined::isDefault($schema->items)) {
                $schema->items = new OA\Items(['_context' => new Context(['generated' => true], $schema->_context)]);
                $this->applyToAnnotation($schema->items, $schemaType->items, $analysis, $sourceClass);
                $this->type2ref($schema->items, $analysis, $sourceClass);
                $analysis->addAnnotation($schema->items, $schema->items->_context);
            } elseif (Undefined::isDefault($schema->items->type, $schema->items->oneOf, $schema->items->allOf, $schema->items->anyOf)) {
                $this->applyToAnnotation($schema->items, $schemaType->items, $analysis, $sourceClass);
                $this->type2ref($schema->items, $analysis, $sourceClass);
            }
            $this->handlePostAugment($schema->items);
        }

        if ($schemaType->additionalProperties instanceof SchemaType) {
            $schema->type = 'object';
            // An explicit `true|false` is the whole answer for this map — nothing for the
            // inferred value type to attach to, and hybrid and spec already keep it.
            // `isDefault()` only rules out the UNDEFINED sentinel, so without this guard a
            // declared `false` is dereferenced as a Schema.
            if (!is_bool($schema->additionalProperties)) {
                if (Undefined::isDefault($schema->additionalProperties)) {
                    $schema->additionalProperties = new OA\AdditionalProperties(['_context' => new Context(['generated' => true], $schema->_context)]);
                    $this->applyToAnnotation($schema->additionalProperties, $schemaType->additionalProperties, $analysis, $sourceClass);
                    $this->type2ref($schema->additionalProperties, $analysis, $sourceClass);
                    $analysis->addAnnotation($schema->additionalProperties, $schema->additionalProperties->_context);
                } elseif (Undefined::isDefault($schema->additionalProperties->type, $schema->additionalProperties->oneOf, $schema->additionalProperties->allOf, $schema->additionalProperties->anyOf)) {
                    $this->applyToAnnotation($schema->additionalProperties, $schemaType->additionalProperties, $analysis, $sourceClass);
                    $this->type2ref($schema->additionalProperties, $analysis, $sourceClass);
                }
                $this->handlePostAugment($schema->additionalProperties);
            }
        } elseif ($schemaType->additionalProperties === true) {
            if (Undefined::isDefault($schema->additionalProperties)) {
                $schema->additionalProperties = new OA\AdditionalProperties(['_context' => new Context(['generated' => true], $schema->_context)]);
                $analysis->addAnnotation($schema->additionalProperties, $schema->additionalProperties->_context);
            }
        }

        if ($schemaType->oneOf !== null) {
            if ($schema->items instanceof OA\Items) {
                return;
            }
            $schema->type = Undefined::UNDEFINED;
            // a member typed with a class that is no component matches anything, and so does the union
            foreach ($schemaType->oneOf as $childType) {
                if ($childType->isRef() && is_string($childType->type) && !$analysis->getAnnotationForSource($childType->type, $sourceClass) instanceof OA\AbstractAnnotation) {
                    return;
                }
            }
            $schema->oneOf = [];
            foreach ($schemaType->oneOf as $childType) {
                $childSchema = new OA\Schema(['_context' => new Context(['generated' => true], $schema->_context)]);
                $this->applyToAnnotation($childSchema, $childType, $analysis, $sourceClass);
                $this->type2ref($childSchema, $analysis, $sourceClass);
                $this->handlePostAugment($childSchema);
                $analysis->addAnnotation($childSchema, $childSchema->_context);
                $schema->oneOf[] = $childSchema;
            }
        }

        if ($schemaType->allOf !== null) {
            $schema->type = Undefined::UNDEFINED;
            $schema->allOf = [];
            foreach ($schemaType->allOf as $childType) {
                $childSchema = new OA\Schema(['_context' => new Context(['generated' => true], $schema->_context)]);
                $this->applyToAnnotation($childSchema, $childType, $analysis, $sourceClass);
                $this->type2ref($childSchema, $analysis, $sourceClass);
                $this->handlePostAugment($childSchema);
                $analysis->addAnnotation($childSchema, $childSchema->_context);
                $schema->allOf[] = $childSchema;
            }
        }

        if ($schemaType->properties !== null) {
            $schema->type = 'object';
            $properties = [];
            foreach ($schemaType->properties as $name => $propType) {
                $property = new OA\Property([
                    'property' => $name,
                    '_context' => new Context(['generated' => true], $schema->_context),
                ]);
                $this->applyToAnnotation($property, $propType, $analysis, $sourceClass);
                $this->type2ref($property, $analysis, $sourceClass);
                $this->handlePostAugment($property);
                $analysis->addAnnotation($property, $property->_context);
                $properties[] = $property;
            }
            $schema->properties = $properties;

            if ($schemaType->required !== null && [] !== $schemaType->required) {
                $schema->required = $schemaType->required;
            }
        }
    }
}
