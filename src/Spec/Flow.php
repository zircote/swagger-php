<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

#[\Attribute(\Attribute::IS_REPEATABLE)]
class Flow extends AbstractAttribute
{
    /**
     * @param array<string,string>|null $scopes
     * @param array<string,mixed>|null  $x
     */
    public function __construct(
        public ?string $flow = null,
        public ?string $authorizationUrl = null,
        public ?string $tokenUrl = null,
        public ?string $refreshUrl = null,
        public ?array $scopes = null,
        ?array $x = null,
    ) {
        parent::__construct(x: $x);
    }

    public function merge(): array
    {
        return [SecurityScheme::class];
    }
}
