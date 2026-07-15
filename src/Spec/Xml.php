<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

/**
 * Metadata for XML representation of a schema property.
 *
 * @see [XML Object](https://spec.openapis.org/oas/v3.1.1.html#xml-object)
 */
#[\Attribute]
class Xml extends AbstractAttribute
{
    /**
     * @param string|null              $name        Replaces the name of the element/attribute
     * @param string|null              $namespace   The URI of the XML namespace
     * @param string|null              $prefix      The namespace prefix to use
     * @param bool|null                $attribute   Whether the property translates to an XML attribute
     * @param bool|null                $wrapped     Whether array items are wrapped in an additional element
     * @param array<string,mixed>|null $x           Vendor extensions (x-* properties)
     * @param list<Attachable>|null    $attachables Reusable custom attachable attributes
     */
    public function __construct(
        public ?string $name = null,
        public ?string $namespace = null,
        public ?string $prefix = null,
        public ?bool $attribute = null,
        public ?bool $wrapped = null,
        ?array $x = null,
        ?array $attachables = null,
    ) {
        parent::__construct(x: $x, attachables: $attachables);
    }

    public function merge(): array
    {
        return [Schema::class => 'xml'];
    }
}
