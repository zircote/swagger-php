<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter;

use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Type\SchemaType;
use OpenApi\Type\TypeResolver;
use OpenApi\Utils\PipeInterface;

/**
 * Infers schema type, format, nullable, items, etc. from PHP type declarations and docblocks.
 *
 * Walks all properties and parameters in the specification and fills their schema
 * fields from the attached reflector's type information.
 *
 * @implements PipeInterface<Specification>
 */
class Type implements PipeInterface
{
    protected TypeResolver $typeResolver;

    public function __construct(?TypeResolver $typeResolver = null)
    {
        $this->typeResolver = $typeResolver ?? new TypeResolver();
    }

    public function group(): string|\BackedEnum
    {
        return Group::Resolve;
    }

    public function __invoke(mixed $payload): mixed
    {
        foreach ($payload->schemas as $schema) {
            $this->walkSchema($schema);
        }

        foreach ($payload->operations as $operation) {
            $this->augmentOperationParameters($operation);
            $this->walkOperationSchemas($operation);
        }

        foreach ($payload->parameters as $parameter) {
            $this->augmentParameter($parameter);
        }

        foreach ($payload->requestBodies as $requestBody) {
            $this->walkMediaTypes($requestBody->content);
        }

        foreach ($payload->headers as $header) {
            $this->walkSchema($header->schema);
        }

        return null;
    }

    protected function walkSchema(?OA\Schema $schema): void
    {
        if (!$schema instanceof OA\Schema) {
            return;
        }

        if ($schema->properties) {
            foreach ($schema->properties as $property) {
                if (!$property instanceof OA\Property) {
                    continue;
                }

                $this->augmentProperty($property);

                if ($property->schema instanceof OA\Schema) {
                    $this->walkSchema($property->schema);
                }
            }
        }

        $this->walkSchema($schema->items instanceof OA\Schema ? $schema->items : null);
        $this->walkSchema($schema->additionalProperties instanceof OA\Schema ? $schema->additionalProperties : null);

        foreach ($schema->allOf ?? [] as $child) {
            $this->walkSchema($child);
        }
        foreach ($schema->anyOf ?? [] as $child) {
            $this->walkSchema($child);
        }
        foreach ($schema->oneOf ?? [] as $child) {
            $this->walkSchema($child);
        }
    }

    protected function walkOperationSchemas(OA\Operation $operation): void
    {
        foreach ($operation->responses ?? [] as $response) {
            $this->walkMediaTypes($response->content);
        }

        if ($operation->requestBody instanceof OA\RequestBody) {
            $this->walkMediaTypes($operation->requestBody->content);
        }
    }

    /**
     * @param list<OA\MediaType>|null $mediaTypes
     */
    protected function walkMediaTypes(?array $mediaTypes): void
    {
        if (!$mediaTypes) {
            return;
        }

        foreach ($mediaTypes as $mediaType) {
            $this->walkSchema($mediaType->schema);
        }
    }

    protected function augmentProperty(OA\Property $property): void
    {
        $reflector = $property->getReflector();
        if (!$reflector instanceof \ReflectionProperty
            && !$reflector instanceof \ReflectionParameter
            && !$reflector instanceof \ReflectionClassConstant
        ) {
            return;
        }

        if ($property->property === null) {
            $property->property = $reflector->getName();
        }

        if ($reflector instanceof \ReflectionClassConstant) {
            return;
        }

        if ($property->schema instanceof OA\Schema && $property->schema->ref !== null) {
            return;
        }

        $resolved = $this->typeResolver->resolve($reflector);
        if (!$resolved instanceof SchemaType) {
            return;
        }

        if (!$property->schema instanceof OA\Schema) {
            $property->schema = $this->schemaTypeToSchema($resolved);
        } else {
            $this->mergeIntoSchema($property->schema, $resolved);
        }
    }

    protected function augmentOperationParameters(OA\Operation $operation): void
    {
        if (!$operation->parameters) {
            return;
        }

        foreach ($operation->parameters as $parameter) {
            $this->augmentParameter($parameter);
        }
    }

    protected function augmentParameter(OA\Parameter $parameter): void
    {
        $reflector = $parameter->getReflector();
        if (!$reflector instanceof \ReflectionParameter) {
            return;
        }

        if ($parameter->name === null) {
            $parameter->name = $reflector->getName();
        }

        if ($parameter->schema instanceof OA\Schema && $parameter->schema->ref !== null) {
            return;
        }

        $resolved = $this->typeResolver->resolve($reflector);
        if (!$resolved instanceof SchemaType) {
            return;
        }

        if ($parameter->required === true) {
            $resolved->nullable = null;
        }

        if (!$parameter->schema instanceof OA\Schema) {
            $parameter->schema = $this->schemaTypeToSchema($resolved);
        } else {
            $this->mergeIntoSchema($parameter->schema, $resolved);
        }

        if ($parameter->required === null) {
            $parameter->required = $resolved->nullable !== true;
        }
    }

    protected function schemaTypeToSchema(SchemaType $schemaType): OA\Schema
    {
        $schema = new OA\Schema();
        $this->applySchemaType($schema, $schemaType);

        return $schema;
    }

    protected function mergeIntoSchema(OA\Schema $schema, SchemaType $schemaType): void
    {
        if ($schema->type === null && $schema->oneOf === null && $schema->allOf === null && $schema->anyOf === null) {
            $this->applySchemaType($schema, $schemaType);
        }
    }

    protected function applySchemaType(OA\Schema $schema, SchemaType $schemaType): void
    {
        if ($schemaType->nullable !== null && $schema->nullable === null) {
            $schema->nullable = $schemaType->nullable;
        }

        if ($schemaType->type !== null) {
            if ($schemaType->isRef()) {
                $schema->ref = $schemaType->type;
            } else {
                $schema->type = $schemaType->type;
            }
        }

        if ($schemaType->format !== null && $schema->format === null) {
            $schema->format = $schemaType->format;
        }

        if ($schemaType->minimum !== null && $schema->minimum === null) {
            $schema->minimum = $schemaType->minimum;
        }

        if ($schemaType->maximum !== null && $schema->maximum === null) {
            $schema->maximum = $schemaType->maximum;
        }

        if ($schemaType->not !== null && !$schema->not instanceof OA\Schema) {
            $schema->not = new OA\Schema(const: $schemaType->not['const']);
        }

        if ($schemaType->items instanceof SchemaType) {
            $schema->type = 'array';
            if ($schema->items === null) {
                $schema->items = $this->schemaTypeToSchema($schemaType->items);
            }
        }

        if ($schemaType->additionalProperties instanceof SchemaType) {
            if ($schema->additionalProperties === null) {
                $schema->additionalProperties = $this->schemaTypeToSchema($schemaType->additionalProperties);
            }
        } elseif ($schemaType->additionalProperties === true) {
            if ($schema->additionalProperties === null) {
                $schema->additionalProperties = true;
            }
        }

        if ($schemaType->oneOf !== null) {
            $schema->oneOf = array_map($this->schemaTypeToSchema(...), $schemaType->oneOf);
        }

        if ($schemaType->allOf !== null) {
            $schema->allOf = array_map($this->schemaTypeToSchema(...), $schemaType->allOf);
        }

        if ($schemaType->properties !== null) {
            $schema->type = 'object';
            $schema->properties = [];
            foreach ($schemaType->properties as $name => $propType) {
                $propSchema = $this->schemaTypeToSchema($propType);
                $property = new OA\Property(property: $name, schema: $propSchema);
                $schema->properties[] = $property;
            }
            if ($schemaType->required !== null) {
                $schema->required = $schemaType->required;
            }
        }
    }
}
