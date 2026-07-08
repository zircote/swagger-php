<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

#[\Attribute(\Attribute::TARGET_CLASS)]
class License extends AbstractAttribute
{
    /**
     * @param array<string,mixed>|null $x
     */
    public function __construct(
        public ?string $name = null,
        public ?string $identifier = null,
        public ?string $url = null,
        ?array $x = null,
    ) {
        parent::__construct(x: $x);
    }

    public function allowedParents(): ?array
    {
        return [Info::class];
    }
}
