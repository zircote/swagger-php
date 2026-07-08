<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class RequestBody extends AbstractAttribute
{
    /**
     * @param list<MediaType>|null     $content
     * @param array<string,mixed>|null $x
     */
    public function __construct(
        public ?string $request = null,
        public ?string $description = null,
        public ?bool $required = null,
        public ?string $ref = null,
        public ?array $content = null,
        ?array $x = null,
    ) {
        parent::__construct(x: $x);
    }

    public function allowedParents(): ?array
    {
        return [Operation::class];
    }
}
