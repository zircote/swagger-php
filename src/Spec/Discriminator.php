<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

/**
 * Aids in serialization, deserialization, and validation when request bodies or responses
 * can be one of several schemas (used with oneOf, anyOf, allOf).
 *
 * @see [Discriminator Object](https://spec.openapis.org/oas/v3.1.1.html#discriminator-object)
 */
#[\Attribute]
class Discriminator extends AbstractAttribute
{
    /**
     * @param string|null               $propertyName The name of the property in the payload that distinguishes types
     * @param array<string,string>|null $mapping      Maps payload values to schema names or references
     * @param array<string,mixed>|null  $x            Vendor extensions (x-* properties)
     * @param list<Attachable>|null     $attachables  Reusable custom attachable attributes
     * @param Fan|null                  $fan          Where this attribute goes when more than one attribute could hold it
     */
    public function __construct(
        public ?string $propertyName = null,
        public ?array $mapping = null,
        ?array $x = null,
        ?array $attachables = null,
        ?Fan $fan = null,
    ) {
        parent::__construct(x: $x, attachables: $attachables, fan: $fan);
    }

    public function merge(): array
    {
        return [Schema::class => 'discriminator'];
    }
}
