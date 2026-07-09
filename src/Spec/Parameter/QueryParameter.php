<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec\Parameter;

use OpenApi\Spec;
use OpenApi\Undefined;

/**
 * A parameter passed via the URL query string.
 *
 * @see [Parameter Object](https://spec.openapis.org/oas/v3.1.1.html#parameter-object)
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_PARAMETER | \Attribute::IS_REPEATABLE)]
class QueryParameter extends Spec\Parameter
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
        ?bool $allowEmptyValue = null,
        ?string $ref = null,
        ?string $style = null,
        ?bool $explode = null,
        ?bool $allowReserved = null,
        ?Spec\Schema $schema = null,
        mixed $example = Undefined::UNDEFINED,
        ?array $examples = null,
        ?array $content = null,
        ?array $x = null,
    ) {
        parent::__construct(
            parameter: $parameter,
            name: $name,
            in: 'query',
            description: $description,
            required: $required,
            deprecated: $deprecated,
            allowEmptyValue: $allowEmptyValue,
            ref: $ref,
            style: $style,
            explode: $explode,
            allowReserved: $allowReserved,
            schema: $schema,
            example: $example,
            examples: $examples,
            content: $content,
            x: $x,
        );
    }
}
