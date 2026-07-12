<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

/**
 * Defines a single property within a Schema object.
 *
 * @see [Schema Object](https://spec.openapis.org/oas/v3.1.1.html#schema-object)
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::TARGET_PARAMETER | \Attribute::TARGET_CLASS_CONSTANT | \Attribute::IS_REPEATABLE)]
class Property extends AbstractAttribute
{
    /**
     * @param string|null              $property The property name
     * @param Schema|null              $schema   The schema defining the property type and constraints
     * @param array<string,mixed>|null $x        Vendor extensions (x-* properties)
     */
    public function __construct(
        public ?string $property = null,
        public ?Schema $schema = null,
        ?array $x = null,
    ) {
        parent::__construct(x: $x);
    }

    public function merge(): array
    {
        return [];
    }
}
