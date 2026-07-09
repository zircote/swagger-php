<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec\Parameter;

use OpenApi\Spec as OA;
use OpenApi\Undefined;

/**
 * A parameter passed via the URL path (always required).
 *
 * @see [Parameter Object](https://spec.openapis.org/oas/v3.1.1.html#parameter-object)
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_PARAMETER | \Attribute::IS_REPEATABLE)]
class Path extends OA\Parameter
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
        ?bool $deprecated = null,
        ?string $ref = null,
        ?string $style = null,
        ?bool $explode = null,
        ?OA\Schema $schema = null,
        mixed $example = Undefined::UNDEFINED,
        ?array $examples = null,
        ?array $content = null,
        ?array $x = null,
    ) {
        parent::__construct(
            parameter: $parameter,
            name: $name,
            in: 'path',
            description: $description,
            required: true,
            deprecated: $deprecated,
            ref: $ref,
            style: $style,
            explode: $explode,
            schema: $schema,
            example: $example,
            examples: $examples,
            content: $content,
            x: $x,
        );
    }
}
