<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Augmenter;

use OpenApi\Contracts\TypeResolverAwareInterface;
use OpenApi\HybridBridge;
use OpenApi\Spec as OA;
use OpenApi\Specification;
use OpenApi\Specification\ComponentIndex;
use OpenApi\Specification\ComponentKey;
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
 * Fills the schema of each property, parameter and request body that
 * `Specification\Walker::eachTypedAttribute()` lists, from its reflector's type information.
 *
 * @implements PipeInterface<Specification>
 */
class Types implements PipeInterface, LoggerAwareInterface, TypeResolverAwareInterface
{
    protected ?LoggerInterface $logger = null;

    /** the components a class type is looked up in, by its plain key, in the bucket its use means */
    protected ?ComponentIndex $index = null;

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
        $this->index = $payload->buildComponentIndex();
        $this->inferredTypes = new \SplObjectStorage();

        $walker = $payload->getWalker();
        $walker->eachSchema($this->inferSchemaType(...));
        $walker->eachTypedAttribute(function (OA\Property|OA\Parameter|OA\RequestBody $attribute): void {
            match (true) {
                $attribute instanceof OA\Property => $this->augmentProperty($attribute),
                $attribute instanceof OA\Parameter => $this->augmentParameter($attribute),
                $attribute instanceof OA\RequestBody => $this->augmentRequestBody($attribute),
            };
        });

        return null;
    }

    public function setTypeResolver(TypeResolver $typeResolver): static
    {
        $this->typeResolver = $typeResolver;
        if ($this->logger instanceof LoggerInterface) {
            $this->typeResolver->setLogger($this->logger);
        }

        return $this;
    }

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

    protected function augmentRequestBody(OA\RequestBody $requestBody): void
    {
        $reflector = $requestBody->getReflector();
        if ($reflector instanceof \ReflectionParameter) {
            $resolved = $this->typeResolver->resolve($reflector);

            if ($resolved instanceof SchemaType) {
                if ($resolved->type !== null && $resolved->isRef()) {
                    $this->refRequestBody($requestBody, $resolved->type, $reflector);
                }

                if ($resolved->nullable !== null && $requestBody->required === null) {
                    $requestBody->required = !$resolved->nullable;
                }
            }
        }
    }

    protected function augmentProperty(OA\Property $property): void
    {
        $reflector = $property->getReflector();
        if ($reflector instanceof \ReflectionMethod) {
            $this->augmentTypedProperty($property, $reflector);

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

        $this->augmentTypedProperty($property, $reflector);
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

    /**
     * Types a property from the PHP type of the member behind it, unless its schema is a reference.
     */
    protected function augmentTypedProperty(OA\Property $property, \ReflectionProperty|\ReflectionParameter|\ReflectionMethod $reflector): void
    {
        if ($property->schema instanceof OA\Schema && $property->schema->ref !== null) {
            return;
        }

        $resolved = $this->typeResolver->resolve($reflector);
        if (!$resolved instanceof SchemaType) {
            return;
        }

        $this->subject = $reflector;
        $property->schema = $this->typedSchema($property->schema, $resolved);
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

        // a querystring parameter is described by its content, so it takes no schema
        if ($parameter->in !== OA\ParameterIn::Querystring->value) {
            $parameter->schema = $this->typedSchema($parameter->schema, $resolved);
        }

        // a default means a value exists when the parameter is left out, and one converted from classic
        // keeps classic's rule. A path parameter has `required` already, from `PathParameters`
        $hasDefault = $reflector->isDefaultValueAvailable()
            && $parameter->getMeta(HybridBridge::class) !== true;

        $parameter->required ??= !$isNullable && !$hasDefault;
    }

    /**
     * Points a request body at the request body component its parameter's class names.
     *
     * One left with neither a ref nor content is reported, since it describes nothing.
     */
    protected function refRequestBody(OA\RequestBody $requestBody, string $class, \ReflectionParameter $reflector): void
    {
        $count = $this->countComponents($class, 'requestBodies');
        if ($count === 1) {
            $requestBody->ref = $class;

            return;
        }

        if ($requestBody->ref !== null || $requestBody->content !== null) {
            return;
        }

        $subject = $this->typeResolver->describe($reflector);
        $this->logger?->error(match (true) {
            $count > 1 => "Request body \"{$class}\" in {$subject} matches several request bodies, left empty; reference one by name",
            $this->countComponents($class) === 1 => "Request body \"{$class}\" in {$subject} matches no request body, left empty; its schema belongs in the request body's content",
            default => "Request body \"{$class}\" in {$subject} matches no request body, left empty",
        });
    }

    /**
     * How many components a class names by its plain key in a bucket.
     *
     * The class references one only when it is the only one.
     */
    protected function countComponents(string $class, string $bucket = 'schemas'): int
    {
        return count($this->index?->findAllByKey(new ComponentKey(ltrim($class, '\\')), $bucket) ?? []);
    }

    /**
     * Whether a class ref matching `$count` components falls back to its `unmatched` type: a
     * collection class with no component is the collection it is.
     */
    protected function fallsBack(SchemaType $schemaType, int $count): bool
    {
        return $count === 0 && $schemaType->unmatched instanceof SchemaType;
    }

    /**
     * The schema typed with the resolved type: a new one where there is none, else the one given.
     */
    protected function typedSchema(?OA\Schema $schema, SchemaType $schemaType): OA\Schema
    {
        if (!$schema instanceof OA\Schema) {
            return $this->schemaTypeToSchema($schemaType);
        }

        $this->mergeIntoSchema($schema, $schemaType);

        return $schema;
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
        } elseif (!$this->hasExplicitType($schema) && $schema->items instanceof OA\Schema && $schemaType->items instanceof SchemaType) {
            // an array by its `items` or by shortcut, so the items are what is left to fill
            $this->mergeIntoNested($schema->items, $schemaType->items);
        }

        // a type the author wrote out is the whole type, so the PHP type's `null` is not added to it
        if (!$this->hasExplicitType($schema)) {
            $schema->nullable ??= $schemaType->nullable;
        }
    }

    /**
     * Fills an `items` or `additionalProperties` schema that has no type of its own from the PHP
     * type, keeping what the author wrote. One with a `ref` stays as written.
     */
    protected function mergeIntoNested(OA\Schema $schema, SchemaType $schemaType): void
    {
        if ($schema->ref === null) {
            $this->mergeIntoSchema($schema, $schemaType);
        }
    }

    /**
     * The type without `null`, for a schema whose author set `nullable: false`.
     *
     * @param  string|list<string> $type
     * @return string|list<string>
     */
    protected function withoutNull(string|array $type): string|array
    {
        $types = array_values(array_diff((array) $type, ['null']));

        return match (count($types)) {
            0 => $type,
            1 => $types[0],
            default => $types,
        };
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

    /**
     * Reports a referenced class that matches no component, or several.
     *
     * @param string $omitted what is left out because of it
     */
    protected function logUnmatchedClass(string $class, int $count, string $omitted): void
    {
        $subject = $this->subject instanceof \Reflector ? $this->typeResolver->describe($this->subject) : 'a schema';
        $this->logger?->error($count === 0
            ? "Type \"{$class}\" in {$subject} matches no schema, {$omitted}"
            : "Type \"{$class}\" in {$subject} matches several schemas, {$omitted}; reference one by name");
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
                    $count = $this->countComponents($schemaType->type);
                    if ($count === 1) {
                        $schema->ref = $schemaType->type;
                    } elseif ($this->fallsBack($schemaType, $count)) {
                        $this->applySchemaType($schema, $schemaType->unmatched);
                    } else {
                        $this->logUnmatchedClass($schemaType->type, $count, 'omitted');
                    }
                }
            } else {
                $schema->type = $schema->nullable === false ? $this->withoutNull($schemaType->type) : $schemaType->type;
            }
        }

        $schema->format ??= $schemaType->format;
        $schema->minimum ??= $schemaType->minimum;
        $schema->maximum ??= $schemaType->maximum;
        $schema->minLength ??= $schemaType->minLength;

        if ($schemaType->not !== null && !$schema->not instanceof OA\Schema) {
            $schema->not = new OA\Schema(const: $schemaType->not['const']);
        }

        if ($schemaType->items instanceof SchemaType) {
            $schema->type = 'array';
            $schema->items ??= $this->schemaTypeToSchema($schemaType->items);
        }

        if ($schemaType->additionalProperties instanceof SchemaType) {
            if ($schema->additionalProperties instanceof OA\Schema) {
                $this->mergeIntoNested($schema->additionalProperties, $schemaType->additionalProperties);
            } else {
                $schema->additionalProperties ??= $this->schemaTypeToSchema($schemaType->additionalProperties);
            }
        } elseif ($schemaType->additionalProperties === true) {
            $schema->additionalProperties ??= true;
        }

        if ($schemaType->oneOf !== null) {
            // a member typed with a class that is no component matches anything, and so does the union
            $unmatched = [];
            foreach ($schemaType->oneOf as $member) {
                if (!$member->isRef()) {
                    continue;
                }

                $count = $this->countComponents($member->type);
                if ($count === 1 || $this->fallsBack($member, $count)) {
                    continue;
                }

                $unmatched[$member->type] = $count;
            }

            if ($unmatched === []) {
                $schema->oneOf = array_map($this->schemaTypeToSchema(...), $schemaType->oneOf);
            } else {
                foreach ($unmatched as $class => $count) {
                    $this->logUnmatchedClass($class, $count, 'omitted with the rest of its union');
                }
            }
        }

        if ($schemaType->allOf !== null) {
            $schema->allOf = array_map($this->schemaTypeToSchema(...), $schemaType->allOf);
        }

        if ($schemaType->anyOf !== null) {
            $schema->anyOf = array_map($this->schemaTypeToSchema(...), $schemaType->anyOf);
        }

        if ($schemaType->properties !== null) {
            $schema->type = 'object';
            $schema->properties = [];
            foreach ($schemaType->properties as $name => $propertyType) {
                $schema->properties[] = new OA\Property(property: $name, schema: $this->schemaTypeToSchema($propertyType));
            }
            if ($schemaType->required !== null) {
                $schema->required = $schemaType->required;
            }
        }
    }
}
