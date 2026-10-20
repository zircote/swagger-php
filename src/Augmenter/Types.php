<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter;

use OpenApi\HybridBridge;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Type\SchemaType;
use OpenApi\Type\TypeMapper;
use OpenApi\Type\TypeResolver;
use OpenApi\Undefined;
use OpenApi\Utils\PipeInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

/**
 * Infers schema type, format, nullable, items, etc. from PHP type declarations and docblocks.
 *
 * Walks all properties and parameters in the specification and fills their schema
 * fields from the attached reflector's type information.
 *
 * @implements PipeInterface<Specification>
 */
class Types implements PipeInterface, LoggerAwareInterface
{
    protected ?LoggerInterface $logger = null;

    /** @var array<string, string> class name => component pointer, for the components that exist */
    protected array $refMap = [];

    /** the member whose type is being applied, for the warning when a class in it has no schema */
    protected ?\Reflector $subject = null;

    /** @var \SplObjectStorage<OA\Schema, null> schemas whose `type` this augmenter inferred, not the author */
    protected \SplObjectStorage $inferredTypes;

    public function __construct(
        protected TypeResolver $typeResolver = new TypeResolver(),
    ) {
    }

    public function __invoke(mixed $payload): mixed
    {
        $this->refMap = $payload->buildComponentIndex()->buildRefMap();
        $this->inferredTypes = new \SplObjectStorage();

        $payload->getWalker()->eachSchema(function (OA\Schema $schema): void {
            $this->inferSchemaType($schema);
            $this->walkSchema($schema);
        });

        foreach ($payload->operations as $operation) {
            $this->augmentOperationParameters($operation);
            $this->walkOperationSchemas($operation);
        }

        foreach ($payload->parameters as $parameter) {
            $this->augmentParameter($parameter);
        }

        foreach ($payload->requestBodies as $requestBody) {
            $this->augmentRequestBody($requestBody);
        }

        foreach ($payload->responses as $response) {
            $this->walkMediaTypes($response->content);
        }

        foreach ($payload->headers as $header) {
            $this->walkSchema($header->schema);
        }

        return null;
    }

    /**
     * Override the type resolver used to infer schema types from PHP type declarations.
     */
    public function setTypeResolver(TypeResolver $typeResolver): static
    {
        $this->typeResolver = $typeResolver;
        if ($this->logger instanceof LoggerInterface) {
            $this->typeResolver->setLogger($this->logger);
        }

        return $this;
    }

    /**
     * Passes the logger to the type resolver, including one set later.
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
        $this->typeResolver->setLogger($logger);
    }

    public function group(): string|\BackedEnum
    {
        return Group::Resolve;
    }

    protected function inferSchemaType(OA\Schema $schema): void
    {
        if ($schema->type !== null) {
            return;
        }

        if ($schema->items instanceof OA\Schema) {
            $schema->type = 'array';
            $this->inferredTypes->offsetSet($schema);
        } elseif ($schema->properties || $schema->patternProperties) {
            $schema->type = 'object';
            $this->inferredTypes->offsetSet($schema);
        }
    }

    protected function walkSchema(?OA\Schema $schema): void
    {
        if (!$schema instanceof OA\Schema) {
            return;
        }

        $this->inferSchemaType($schema);

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
            $this->augmentRequestBody($operation->requestBody);
        }
    }

    protected function augmentRequestBody(OA\RequestBody $requestBody): void
    {
        $this->walkMediaTypes($requestBody->content);

        $reflector = $requestBody->getReflector();
        if ($reflector instanceof \ReflectionParameter) {
            $resolved = $this->typeResolver->resolve($reflector);

            if ($resolved instanceof SchemaType) {
                if ($resolved->type !== null && $resolved->isRef() && $this->isComponent($resolved->type)) {
                    $requestBody->ref = $resolved->type;
                }

                if ($resolved->nullable !== null && $requestBody->required === null) {
                    $requestBody->required = !$resolved->nullable;
                }
            }
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
        if ($reflector instanceof \ReflectionMethod) {
            $this->augmentMethodProperty($property, $reflector);

            return;
        }

        if (!$reflector instanceof \ReflectionProperty
            && !$reflector instanceof \ReflectionParameter
            && !$reflector instanceof \ReflectionClassConstant
        ) {
            return;
        }

        $property->property ??= $reflector->getName();

        if ($reflector instanceof \ReflectionClassConstant) {
            $this->augmentConstantProperty($property, $reflector);

            return;
        }

        if ($property->schema instanceof OA\Schema && $property->schema->ref !== null) {
            return;
        }

        $resolved = $this->typeResolver->resolve($reflector);
        if (!$resolved instanceof SchemaType) {
            return;
        }

        $this->subject = $reflector;

        if (!$property->schema instanceof OA\Schema) {
            $property->schema = $this->schemaTypeToSchema($resolved);
        } else {
            $this->mergeIntoSchema($property->schema, $resolved);
        }
    }

    protected function augmentConstantProperty(OA\Property $property, \ReflectionClassConstant $reflector): void
    {
        $value = $reflector->getValue();

        $schema = $property->schema ?? new OA\Schema();
        // Can't use ??= here — const defaults to Undefined::UNDEFINED, not null
        if (Undefined::isDefault($schema->const)) {
            $schema->const = $value;
        }
        // an array constant stays untyped, as it always has
        $schema->type ??= is_array($value) ? null : TypeMapper::valueType($value);
        $property->schema = $schema;
    }

    protected function augmentMethodProperty(OA\Property $property, \ReflectionMethod $method): void
    {
        if ($property->schema instanceof OA\Schema && $property->schema->ref !== null) {
            return;
        }

        $resolved = $this->typeResolver->resolve($method);
        if (!$resolved instanceof SchemaType) {
            return;
        }

        $this->subject = $method;

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
            $this->walkMediaTypes($parameter->content);
        }
    }

    protected function augmentParameter(OA\Parameter $parameter): void
    {
        $reflector = $parameter->getReflector();
        if (!$reflector instanceof \ReflectionParameter) {
            return;
        }

        $parameter->name ??= $reflector->getName();

        if ($parameter->schema instanceof OA\Schema && $parameter->schema->ref !== null) {
            return;
        }

        $resolved = $this->typeResolver->resolve($reflector);
        if (!$resolved instanceof SchemaType) {
            return;
        }

        $isNullable = $resolved->nullable === true;

        // PHP nullable type on a parameter means "can be omitted" (required: false),
        // not "accepts null when present" — suppress nullable on the schema
        if ($parameter->required === true || $isNullable) {
            $resolved->nullable = null;
        }

        $this->subject = $reflector;

        if (!$parameter->schema instanceof OA\Schema) {
            $parameter->schema = $this->schemaTypeToSchema($resolved);
        } else {
            $this->mergeIntoSchema($parameter->schema, $resolved);
        }

        // a default means a value exists when the parameter is left out. A path parameter is required
        // whatever its default, and one converted from classic keeps classic's rule
        $hasDefault = $reflector->isDefaultValueAvailable()
            && $parameter->in !== 'path'
            && $parameter->getMeta(HybridBridge::class) !== true;

        $parameter->required ??= !$isNullable && !$hasDefault;
    }

    protected function isComponent(string $class): bool
    {
        return isset($this->refMap[ltrim($class, '\\')]);
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

        // a type the author wrote out is the whole type, so the PHP type's `null` is not added to it
        if (!$this->hasExplicitType($schema)) {
            $schema->nullable ??= $schemaType->nullable;
        }
    }

    /**
     * Whether the author gave the schema its type, rather than a shortcut or this augmenter.
     *
     * `Schema\Items` is always an array, whatever its arguments say, and `inferSchemaType()` types
     * a schema from its `items` or `properties`.
     */
    protected function hasExplicitType(OA\Schema $schema): bool
    {
        if ($schema->oneOf !== null || $schema->allOf !== null || $schema->anyOf !== null) {
            return true;
        }

        return $schema->type !== null
            && !$schema instanceof OA\Schema\Items
            && !$this->inferredTypes->offsetExists($schema);
    }

    protected function applySchemaType(OA\Schema $schema, SchemaType $schemaType): void
    {
        if ($schemaType->nullable !== null && $schema->nullable === null) {
            $schema->nullable = $schemaType->nullable;
        }

        if ($schemaType->type !== null) {
            if ($schemaType->isRef()) {
                // a class is referenced only when it is a component, and never in place of an explicit enum
                if ($schema->enum === null) {
                    if ($this->isComponent($schemaType->type)) {
                        $schema->ref = $schemaType->type;
                    } else {
                        $subject = $this->subject instanceof \Reflector ? $this->typeResolver->describe($this->subject) : 'a schema';
                        $this->logger?->warning("No schema for {$schemaType->type} in {$subject}, so its type is left out");
                    }
                }
            } else {
                $schema->type = $schemaType->type;
            }
        }

        $schema->format ??= $schemaType->format;

        $schema->minimum ??= $schemaType->minimum;

        $schema->maximum ??= $schemaType->maximum;

        if ($schemaType->not !== null && !$schema->not instanceof OA\Schema) {
            $schema->not = new OA\Schema(const: $schemaType->not['const']);
        }

        if ($schemaType->items instanceof SchemaType) {
            $schema->type = 'array';
            $schema->items ??= $this->schemaTypeToSchema($schemaType->items);
        }

        if ($schemaType->additionalProperties instanceof SchemaType) {
            $schema->additionalProperties ??= $this->schemaTypeToSchema($schemaType->additionalProperties);
        } elseif ($schemaType->additionalProperties === true) {
            $schema->additionalProperties ??= true;
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
