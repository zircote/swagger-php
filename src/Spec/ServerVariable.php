<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

#[\Attribute(\Attribute::IS_REPEATABLE)]
class ServerVariable extends AbstractAttribute
{
    /**
     * @param list<string>|null        $enum
     * @param array<string,mixed>|null $x
     */
    public function __construct(
        public ?string $serverVariable = null,
        public ?string $default = null,
        public ?string $description = null,
        public ?array $enum = null,
        ?array $x = null,
    ) {
        parent::__construct(x: $x);
    }

    public function allowedParents(): ?array
    {
        return [Server::class];
    }
}
