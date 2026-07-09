<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

/**
 * Metadata about the API.
 *
 * @see [Info Object](https://spec.openapis.org/oas/v3.1.1.html#info-object)
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Info extends AbstractAttribute
{
    /**
     * @param string|null              $title          The title of the API
     * @param string|null              $description    A description of the API (CommonMark syntax)
     * @param string|null              $termsOfService A URL to the Terms of Service for the API
     * @param string|null              $version        The version of the API document
     * @param Contact|null             $contact        Contact information for the API
     * @param License|null             $license        License information for the API
     * @param string|null              $summary        A short summary of the API
     * @param array<string,mixed>|null $x              Vendor extensions (x-* properties)
     */
    public function __construct(
        public ?string $title = null,
        public ?string $description = null,
        public ?string $termsOfService = null,
        public ?string $version = null,
        public ?Contact $contact = null,
        public ?License $license = null,
        public ?string $summary = null,
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
        return [
            Contact::class => 'contact',
            License::class => 'license',
        ];
    }
}
