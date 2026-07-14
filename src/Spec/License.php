<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

/**
 * License information for the exposed API.
 *
 * @see [License Object](https://spec.openapis.org/oas/v3.1.1.html#license-object)
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class License extends AbstractAttribute
{
    /**
     * @param string|null              $name        The license name used for the API
     * @param string|null              $identifier  An SPDX license expression for the API
     * @param string|null              $url         A URL to the license used for the API
     * @param array<string,mixed>|null $x           Vendor extensions (x-* properties)
     * @param list<Attachable>|null    $attachables Reusable custom attachable attributes
     */
    public function __construct(
        public ?string $name = null,
        public ?string $identifier = null,
        public ?string $url = null,
        ?array $x = null,
        ?array $attachables = null,
    ) {
        parent::__construct(x: $x, attachables: $attachables);
    }

    public function merge(): array
    {
        return [Info::class => 'license'];
    }
}
