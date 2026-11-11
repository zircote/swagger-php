<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Type;

use OpenApi\Specification\ComponentKey;

/**
 * Value object describing the resolved OpenAPI schema type for a PHP reflector.
 *
 * Produced by TypeResolver::resolve() and consumed by both the spec-attributes
 * augmenter and the classic annotation type resolver.
 */
class SchemaType
{
    /**
     * @param string|list<string>|null $type                 The OpenAPI type(s) or a FQCN for object refs
     * @param string|null              $format               OpenAPI format (e.g. int32, date-time)
     * @param bool|null                $nullable             Whether the value can be null
     * @param self|null                $items                For array types: the items schema
     * @param self|bool|null           $additionalProperties For map types: the value schema
     * @param list<self>|null          $oneOf                Union composition
     * @param list<self>|null          $allOf                Intersection composition
     * @param list<self>|null          $anyOf                AnyOf composition
     * @param array{const: mixed}|null $not                  NOT constraint (e.g. non-zero-int)
     * @param int|float|null           $minimum              Numeric minimum
     * @param int|float|null           $maximum              Numeric maximum
     * @param array<string, self>|null $properties           Named properties (from array shapes)
     * @param list<string>|null        $required             Required property names (from array shapes)
     * @param TypeArguments|null       $typeArguments        For a generic class: the type arguments it is used with
     */
    public function __construct(
        public string|array|null $type = null,
        public ?string $format = null,
        public ?bool $nullable = null,
        public ?self $items = null,
        public self|bool|null $additionalProperties = null,
        public ?array $oneOf = null,
        public ?array $allOf = null,
        public ?array $anyOf = null,
        public ?array $not = null,
        public int|float|null $minimum = null,
        public int|float|null $maximum = null,
        public ?array $properties = null,
        public ?array $required = null,
        public ?TypeArguments $typeArguments = null,
    ) {
    }

    /**
     * A clone holds clones of the types nested in it, so changing it leaves the original alone.
     */
    public function __clone()
    {
        $copy = static fn (self $type): self => clone $type;

        $this->items = $this->items instanceof self ? clone $this->items : null;
        $this->additionalProperties = $this->additionalProperties instanceof self ? clone $this->additionalProperties : $this->additionalProperties;
        $this->oneOf = $this->oneOf === null ? null : array_map($copy, $this->oneOf);
        $this->allOf = $this->allOf === null ? null : array_map($copy, $this->allOf);
        $this->anyOf = $this->anyOf === null ? null : array_map($copy, $this->anyOf);
        $this->properties = $this->properties === null ? null : array_map($copy, $this->properties);
    }

    /**
     * @phpstan-assert-if-true string $this->type
     */
    public function isRef(): bool
    {
        return is_string($this->type) && !$this->isNativeType();
    }

    /**
     * The key of the component a class type references, with its type arguments where it has
     * them, or null for a type that is no class.
     */
    public function componentKey(): ?ComponentKey
    {
        if (!$this->isRef()) {
            return null;
        }

        return new ComponentKey(
            ltrim($this->type, '\\'),
            $this->typeArguments instanceof TypeArguments ? [ComponentKey::TYPE_ARGUMENTS => (string) $this->typeArguments] : [],
        );
    }

    /**
     * Whether a class anywhere in the type is used with type arguments: itself, its items, the
     * values of a map, a member of a composition or a property of a shape.
     */
    public function hasTypeArguments(): bool
    {
        if ($this->typeArguments instanceof TypeArguments) {
            return true;
        }

        $nested = [
            $this->items,
            $this->additionalProperties,
            ...$this->oneOf ?? [],
            ...$this->allOf ?? [],
            ...$this->anyOf ?? [],
            ...array_values($this->properties ?? []),
        ];
        foreach ($nested as $type) {
            if ($type instanceof self && $type->hasTypeArguments()) {
                return true;
            }
        }

        return false;
    }

    protected function isNativeType(): bool
    {
        if (!is_string($this->type)) {
            return false;
        }

        return in_array($this->type, ['string', 'number', 'integer', 'boolean', 'array', 'object', 'null'], true);
    }
}
