<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

/**
 * Metadata for XML representation of a schema property.
 *
 * From OpenAPI 3.2 on, `nodeType` says which kind of XML node a schema becomes. For example, a
 * price with its currency as an attribute and its amount as the element's text:
 *
 * ```php
 * #[OA\Schema(
 *     component: 'Price',
 *     type: 'object',
 *     properties: [
 *         'currency' => new OA\Schema(type: 'string', xml: new OA\Xml(nodeType: OA\XmlNodeType::Attribute)),
 *         'amount' => new OA\Schema(type: 'number', xml: new OA\Xml(nodeType: OA\XmlNodeType::Text)),
 *     ],
 * )]
 * ```
 *
 * describes something like this:
 *
 * ```xml
 * <Price currency="EUR">12.50</Price>
 * ```
 *
 * Where the node type is the only XML detail, a schema takes it directly:
 * `xml: OA\XmlNodeType::Text` stands for `xml: new OA\Xml(nodeType: OA\XmlNodeType::Text)`.
 *
 * @see [XML Object](https://spec.openapis.org/oas/v3.2.0.html#xml-object)
 */
#[\Attribute]
class Xml extends AbstractAttribute
{
    public ?string $nodeType = null;

    /**
     * @param string|null              $name        Replaces the name of the element/attribute
     * @param string|null              $namespace   The URI of the XML namespace
     * @param string|null              $prefix      The namespace prefix to use
     * @param bool|null                $attribute   Whether the property translates to an XML attribute; from OpenAPI 3.2 on, use `nodeType` instead
     * @param bool|null                $wrapped     Whether array items are wrapped in an additional element; from OpenAPI 3.2 on, use `nodeType` instead
     * @param array<string,mixed>|null $x           Vendor extensions (x-* properties)
     * @param list<Attachable>|null    $attachables Reusable custom attachable attributes
     * @param Fan|null                 $fan         Where this attribute goes when more than one attribute could hold it
     * @param string|XmlNodeType|null  $nodeType    The kind of XML node (element, attribute, text, cdata or none), from OpenAPI 3.2 on. Earlier versions write it as `attribute` or `wrapped` where they can
     */
    public function __construct(
        public ?string $name = null,
        public ?string $namespace = null,
        public ?string $prefix = null,
        public ?bool $attribute = null,
        public ?bool $wrapped = null,
        ?array $x = null,
        ?array $attachables = null,
        ?Fan $fan = null,
        string|XmlNodeType|null $nodeType = null,
    ) {
        $this->nodeType = $nodeType instanceof XmlNodeType ? $nodeType->value : $nodeType;

        parent::__construct(x: $x, attachables: $attachables, fan: $fan);
    }

    public function merge(): array
    {
        return [Schema::class => 'xml'];
    }
}
