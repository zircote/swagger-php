<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

/**
 * Describes the encoding for a single property in a media type.
 *
 * @see [Encoding Object](https://spec.openapis.org/oas/v3.1.1.html#encoding-object)
 */
#[\Attribute(\Attribute::IS_REPEATABLE)]
class Encoding extends AbstractAttribute
{
    /**
     * @param string|null              $encoding      The property name this encoding applies to
     * @param string|null              $contentType   The Content-Type for encoding a specific property
     * @param list<Header>|null        $headers       Additional headers for multipart media types
     * @param string|null              $style         How the property value is serialized
     * @param bool|null                $explode       Whether arrays/objects generate separate parameters
     * @param bool|null                $allowReserved Whether reserved characters are allowed without encoding
     * @param array<string,mixed>|null $x             Vendor extensions (x-* properties)
     */
    public function __construct(
        public ?string $encoding = null,
        public ?string $contentType = null,
        public ?array $headers = null,
        public ?string $style = null,
        public ?bool $explode = null,
        public ?bool $allowReserved = null,
        ?array $x = null,
    ) {
        parent::__construct(x: $x);
    }

    public function merge(): array
    {
        return [MediaType::class => 'encoding[]'];
    }
}
