<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

use OpenApi\Undefined;

#[\Attribute(\Attribute::IS_REPEATABLE)]
class Header extends AbstractAttribute
{
    /**
     * @param list<Example>|null       $examples
     * @param list<MediaType>|null     $content
     * @param array<string,mixed>|null $x
     */
    public function __construct(
        public ?string $header = null,
        public ?string $description = null,
        public ?bool $required = null,
        public ?bool $deprecated = null,
        public ?string $ref = null,
        public ?string $style = null,
        public ?bool $explode = null,
        public ?Schema $schema = null,
        public mixed $example = Undefined::UNDEFINED,
        public ?array $examples = null,
        public ?array $content = null,
        ?array $x = null,
    ) {
        parent::__construct(x: $x);
    }

    public function allowedParents(): ?array
    {
        return [Response::class, Encoding::class];
    }
}
