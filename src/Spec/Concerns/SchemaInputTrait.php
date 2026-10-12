<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec\Concerns;

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
     *
     * @param PropertiesInput|null $properties
     *
     * @return list<Property>|null
     */
    protected static function propertyList(?array $properties): ?array
    {
        if ($properties === null) {
            return null;
        }

        $list = [];
        foreach ($properties as $key => $value) {
            if (is_string($key) && $value instanceof Type) {
                $value = new Schema(type: $value);
            }

            if (is_string($key) && $value instanceof Format) {
                $value = new Schema(type: $value->type(), format: $value);
            }

            $list[] = is_string($key) && $value instanceof Schema
                ? new Property(property: $key, schema: $value)
                : $value;
        }

        return $list;
    }
}
