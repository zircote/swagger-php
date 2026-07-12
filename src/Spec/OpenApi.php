<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

/**
 * The root element of an OpenAPI definition.
 *
 * @see [OpenAPI Object](https://spec.openapis.org/oas/v3.1.1.html#openapi-object)
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class OpenApi extends AbstractAttribute
{
    /**
     * @param string|null                     $version  The OpenAPI specification version (e.g. '3.1.0')
     * @param list<Security\Requirement>|null $security Default security requirements for the API
     * @param array<string,mixed>|null        $x        Vendor extensions (x-* properties)
     */
    public function __construct(
        public ?string $version = null,
        /**
         * @var list<Security\Requirement>|null
         */
        public ?array $security = null,
        ?array $x = null,
    ) {
        parent::__construct(x: $x);
    }

    public function isRoot(): bool
    {
        return true;
    }

    public function contains(): array
    {
        return [Security\Requirement::class => 'security[]'];
    }
}
