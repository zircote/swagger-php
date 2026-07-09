<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

/**
 * Contact information for the exposed API.
 *
 * @see [Contact Object](https://spec.openapis.org/oas/v3.1.1.html#contact-object)
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Contact extends AbstractAttribute
{
    /**
     * @param string|null              $name  The identifying name of the contact person/organization
     * @param string|null              $url   A URL pointing to the contact information
     * @param string|null              $email The email address of the contact person/organization
     * @param array<string,mixed>|null $x     Vendor extensions (x-* properties)
     */
    public function __construct(
        public ?string $name = null,
        public ?string $url = null,
        public ?string $email = null,
        ?array $x = null,
    ) {
        parent::__construct(x: $x);
    }

    public function merge(): array
    {
        return [Info::class => 'contact'];
    }
}
