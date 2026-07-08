<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Server extends AbstractAttribute
{
    /**
     * @param list<ServerVariable>|null $variables
     * @param array<string,mixed>|null  $x
     */
    public function __construct(
        public ?string $url = null,
        public ?string $description = null,
        public ?array $variables = null,
        ?array $x = null,
    ) {
        parent::__construct(x: $x);
    }

    public function allowedParents(): ?array
    {
        return [];
    }
}
