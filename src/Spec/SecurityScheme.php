<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class SecurityScheme extends AbstractAttribute
{
    /**
     * @param list<Flow>|null          $flows
     * @param array<string,mixed>|null $x
     */
    public function __construct(
        public ?string $securityScheme = null,
        public ?string $type = null,
        public ?string $description = null,
        public ?string $name = null,
        public ?string $in = null,
        public ?string $scheme = null,
        public ?string $bearerFormat = null,
        public ?string $openIdConnectUrl = null,
        public ?array $flows = null,
        public ?string $ref = null,
        ?array $x = null,
    ) {
        parent::__construct(x: $x);
    }

    public function allowedParents(): ?array
    {
        return [];
    }
}
