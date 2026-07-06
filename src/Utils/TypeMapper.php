<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Utils;

class TypeMapper
{
    public const NATIVE_TYPE_MAP = [
        'mixed' => 'mixed',
        'string' => 'string',
        'array' => 'array',
        'byte' => ['string', 'byte'],
        'boolean' => 'boolean',
        'bool' => 'boolean',
        'int' => 'integer',
        'integer' => 'integer',
        'long' => ['integer', 'long'],
        'float' => ['number', 'float'],
        'double' => ['number', 'double'],
        'date' => ['string', 'date'],
        'datetime' => ['string', 'date-time'],
        '\\datetime' => ['string', 'date-time'],
        'datetimeimmutable' => ['string', 'date-time'],
        '\\datetimeimmutable' => ['string', 'date-time'],
        'datetimeinterface' => ['string', 'date-time'],
        '\\datetimeinterface' => ['string', 'date-time'],
        'number' => 'number',
        'object' => 'object',
    ];

    /**
     * Map a native PHP type to its OpenAPI type and optional format.
     *
     * @return array{type: string, format: string|null}|null null if the type has no OpenAPI representation
     */
    public function map(string $type): ?array
    {
        $type = strtolower($type);

        if (!array_key_exists($type, self::NATIVE_TYPE_MAP)) {
            return null;
        }

        $mapped = self::NATIVE_TYPE_MAP[$type];

        if (is_array($mapped)) {
            return ['type' => $mapped[0], 'format' => $mapped[1]];
        }

        if ('mixed' === $mapped) {
            return ['type' => 'mixed', 'format' => null];
        }

        return ['type' => $mapped, 'format' => null];
    }

    /**
     * Map a native type to its OpenAPI spec type string only.
     */
    public function toSpecType(string $type): string
    {
        $mapped = array_key_exists(strtolower($type), self::NATIVE_TYPE_MAP)
            ? self::NATIVE_TYPE_MAP[strtolower($type)]
            : $type;

        return is_array($mapped) ? $mapped[0] : $mapped;
    }

    /**
     * Map an array of native types to their spec type strings.
     *
     * @param list<string> $types
     *
     * @return list<string>
     */
    public function toSpecTypes(array $types): array
    {
        return array_map($this->toSpecType(...), $types);
    }

    /**
     * Check whether a native type has a valid OpenAPI representation.
     *
     * Types like mixed, callable, resource, iterable have none.
     */
    public function hasOpenApiType(string $type): bool
    {
        $type = strtolower($type);

        if ('mixed' === $type) {
            return false;
        }

        return 'null' === $type || array_key_exists($type, self::NATIVE_TYPE_MAP);
    }
}
