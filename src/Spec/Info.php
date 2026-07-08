<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Info extends AbstractAttribute
{
    /**
     * @param array<string,mixed>|null $x
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

    public function allowedParents(): ?array
    {
        return [];
    }
}
