<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

#[\Attribute(\Attribute::IS_REPEATABLE)]
class Encoding extends AbstractAttribute
{
    /**
     * @param list<Header>|null        $headers
     * @param array<string,mixed>|null $x
     */
    public function __construct(
        public ?string $encoding = null,
        public ?string $contentType = null,
        public ?array $headers = null,
        public ?string $style = null,
        public ?bool $explode = null,
        public ?bool $allowReserved = null,
        ?array $x = null,
    ) {
        parent::__construct(x: $x);
    }

    public function merge(): array
    {
        return [MediaType::class => 'encoding[]'];
    }
}
