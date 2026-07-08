<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec\Parameter;

use OpenApi\Spec;
use OpenApi\Undefined;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_PARAMETER | \Attribute::IS_REPEATABLE)]
class HeaderParameter extends Spec\Parameter
{
    /**
     * @param list<Spec\Example>|null   $examples
     * @param list<Spec\MediaType>|null $content
     * @param array<string,mixed>|null  $x
     */
    public function __construct(
        ?string $parameter = null,
        ?string $name = null,
        ?string $description = null,
        ?bool $required = null,
        ?bool $deprecated = null,
        ?string $ref = null,
        ?bool $explode = null,
        ?Spec\Schema $schema = null,
        mixed $example = Undefined::UNDEFINED,
        ?array $examples = null,
        ?array $content = null,
        ?array $x = null,
    ) {
        parent::__construct(
            parameter: $parameter,
            name: $name,
            in: 'header',
            description: $description,
            required: $required,
            deprecated: $deprecated,
            ref: $ref,
            style: 'simple',
            explode: $explode,
            schema: $schema,
            example: $example,
            examples: $examples,
            content: $content,
            x: $x,
        );
    }
}
