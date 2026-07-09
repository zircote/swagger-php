<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

use OpenApi\Undefined;

#[\Attribute(\Attribute::IS_REPEATABLE)]
class MediaType extends AbstractAttribute
{
    /**
     * @param list<Example>|null          $examples
     * @param array<string,Encoding>|null $encoding
     * @param array<string,mixed>|null    $x
     */
    public function __construct(
        public ?string $mediaType = null,
        public ?Schema $schema = null,
        public mixed $example = Undefined::UNDEFINED,
        public ?array $examples = null,
        public ?array $encoding = null,
        ?array $x = null,
    ) {
        parent::__construct(x: $x);
    }

    public function merge(): array
    {
        return [
            Response::class => 'content[]',
            RequestBody::class => 'content[]',
            Parameter::class => 'content[]',
        ];
    }

    public function contains(): array
    {
        return [
            Encoding::class => 'encoding[]',
            Example::class => 'examples[]',
        ];
    }
}
