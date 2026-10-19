<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec\Concerns;

use OpenApi\OpenApiException;
use OpenApi\Spec\Format;
use OpenApi\Spec\Property;
use OpenApi\Spec\Schema;
use OpenApi\Spec\Type;

/**
 * Narrows the schema input `type` and `properties` accept to what the model stores.
 *
 * Shared by `Schema` and the media type shortcuts that take the same two keywords.
 *
 * @phpstan-import-type TypeInput from Schema
 * @phpstan-import-type PropertiesInput from Schema
 */
trait SchemaInputTrait
{
    /**
     * Narrows a `type` to the string, or list of strings, the model stores.
     *
     * @param TypeInput|null $type
     *
     * @return string|list<string>|null
     */
    protected static function typeValue(string|Type|array|null $type): string|array|null
    {
        if (!is_array($type)) {
            return $type instanceof Type ? $type->value : $type;
        }

        return array_values(array_map(static fn (string|Type $type): string => $type instanceof Type ? $type->value : $type, $type));
    }

    /**
     * Turns keyed `properties` into the list of `Property` the model stores.
     *
     * A key names the property only where its value is a `Schema`, a `Type` or a `Format`, so a
     * list of `Property` passes through as it always has. A `Format` brings the type it implies.
     * A `Schema`, `Type` or `Format` without a string key has no name, and is an error.
     *
     * @param PropertiesInput|null $properties
     *
     * @return list<Property>|null
     *
     * @throws OpenApiException for a `Schema`, `Type` or `Format` without a string key
     */
    protected static function propertyList(?array $properties): ?array
    {
        if ($properties === null) {
            return null;
        }

        $list = [];
        foreach ($properties as $key => $value) {
            if ($value instanceof Property) {
                $list[] = $value;
                continue;
            }

            if (!is_string($key)) {
                throw new OpenApiException(sprintf(
                    'properties: %s needs a string key that names the property',
                    $value instanceof Schema ? $value::class : $value::class . '::' . $value->name,
                ));
            }

            $schema = match (true) {
                $value instanceof Type => new Schema(type: $value),
                $value instanceof Format => new Schema(type: $value->type(), format: $value),
                default => $value,
            };

            $list[] = new Property(property: $key, schema: $schema);
        }

        return $list;
    }
}
