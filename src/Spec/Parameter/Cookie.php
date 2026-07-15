<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec\Parameter;

use OpenApi\Spec as OA;
use OpenApi\Undefined;

/**
 * A parameter passed via an HTTP cookie.
 *
 * @see [Parameter Object](https://spec.openapis.org/oas/v3.1.1.html#parameter-object)
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_PARAMETER | \Attribute::IS_REPEATABLE)]
class Cookie extends OA\Parameter
{
    /**
     * @param list<OA\Example>|null    $examples
     * @param list<OA\MediaType>|null  $content
     * @param array<string,mixed>|null $x
     * @param list<OA\Attachable>|null $attachables
     */
    public function __construct(
        ?string $parameter = null,
        ?string $name = null,
        ?string $description = null,
        ?bool $required = null,
        ?bool $deprecated = null,
        ?string $ref = null,
        ?bool $explode = null,
        ?OA\Schema $schema = null,
        mixed $example = Undefined::UNDEFINED,
        ?array $examples = null,
        ?array $content = null,
        ?array $x = null,
        ?array $attachables = null,
    ) {
        parent::__construct(
            parameter: $parameter,
            name: $name,
            in: 'cookie',
            description: $description,
            required: $required,
            deprecated: $deprecated,
            ref: $ref,
            explode: $explode,
            schema: $schema,
            example: $example,
            examples: $examples,
            content: $content,
            x: $x,
            attachables: $attachables,
        );
    }
}
