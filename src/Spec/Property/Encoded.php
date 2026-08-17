<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec\Property;

use OpenApi\Spec as OA;

/**
 * Defines a single property within a Schema object.
 *
 * @see [Schema Object](https://spec.openapis.org/oas/v3.1.1.html#schema-object)
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::TARGET_PARAMETER | \Attribute::TARGET_CLASS_CONSTANT | \Attribute::IS_REPEATABLE)]
class Encoded extends OA\Property
{
    public ?OA\Encoding $encoding;

    /**
     * @param string|null              $property    The property name
     * @param OA\Schema|null           $schema      The schema defining the property type and constraints
     * @param array<string,mixed>|null $x           Vendor extensions (x-* properties)
     * @param list<OA\Attachable>|null $attachables Reusable custom attachable attributes
     */
    public function __construct(
        ?string $property = null,
        ?OA\Schema $schema = null,
        ?OA\Encoding $encoding = null,
        ?array $x = null,
        ?array $attachables = null,
    ) {
        parent::__construct(property: $property, schema: $schema, x: $x, attachables: $attachables);

        $this->encoding = $encoding;
    }

    public function merge(): array
    {
        return [];
    }

    public function contained(): array
    {
        return [
            OA\Schema::class => 'properties[]',
        ];
    }
}
