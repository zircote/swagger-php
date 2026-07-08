<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

#[\Attribute(\Attribute::IS_REPEATABLE)]
class Link extends AbstractAttribute
{
    /**
     * @param array<string,mixed>|null $parameters
     * @param array<string,mixed>|null $x
     */
    public function __construct(
        public ?string $link = null,
        public ?string $operationRef = null,
        public ?string $operationId = null,
        public ?array $parameters = null,
        public mixed $requestBody = null,
        public ?string $description = null,
        public ?string $ref = null,
        public ?Server $server = null,
        ?array $x = null,
    ) {
        parent::__construct(x: $x);
    }

    public function allowedParents(): ?array
    {
        return [Response::class];
    }
}
