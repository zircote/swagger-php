<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Type;

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
    ) {
    }

    public function isRef(): bool
    {
        return is_string($this->type) && !$this->isNativeType();
    }

    protected function isNativeType(): bool
    {
        if (!is_string($this->type)) {
            return false;
        }

        return in_array($this->type, ['string', 'number', 'integer', 'boolean', 'array', 'object', 'null'], true);
    }
}
