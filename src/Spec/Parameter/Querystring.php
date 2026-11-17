<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec\Parameter;

use OpenApi\Spec as OA;
use OpenApi\Undefined;

/**
 * The whole query string as one parameter, from OpenAPI 3.2 on.
 *
 * Its media type says how the query string is serialized, so it takes `content` and no `schema`,
 * `style` or `explode`. An operation has at most one, and none beside a query parameter.
 *
 * Allows to shorten this:
 *
 * ```php
 * #[OA\Parameter(
 *     name: 'filter',
 *     in: 'querystring',
 *     content: new OA\MediaType(mediaType: 'application/x-www-form-urlencoded', schema: new OA\Schema(ref: Filter::class)),
 * )]
 * ```
 *
 * to this:
 *
 * ```php
 * #[OA\Parameter\Querystring(
 *     name: 'filter',
 *     content: new OA\MediaType(mediaType: 'application/x-www-form-urlencoded', schema: new OA\Schema(ref: Filter::class)),
 * )]
 * ```
 *
 * @see [Parameter Object](https://spec.openapis.org/oas/v3.2.0.html#parameter-object)
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_PARAMETER | \Attribute::IS_REPEATABLE)]
class Querystring extends OA\Parameter
{
    /**
     * @param OA\MediaType|list<OA\MediaType>|null $content
     * @param list<OA\Example>|null                $examples
     * @param array<string,mixed>|null             $x
     * @param list<OA\Attachable>|null             $attachables
     */
    public function __construct(
        ?string $name = null,
        OA\MediaType|array|null $content = null,
        ?string $description = Undefined::UNDEFINED,
        ?bool $required = null,
        ?bool $deprecated = null,
        string|OA\Schema\Ref|null $ref = null,
        mixed $example = Undefined::UNDEFINED,
        ?array $examples = null,
        ?string $component = null,
        ?array $x = null,
        ?array $attachables = null,
        ?OA\Fan $fan = null,
        ?string $summary = null,
    ) {
        parent::__construct(
            name: $name,
            in: OA\ParameterIn::Querystring,
            description: $description,
            required: $required,
            deprecated: $deprecated,
            ref: $ref,
            example: $example,
            examples: $examples,
            content: $content,
            component: $component,
            x: $x,
            attachables: $attachables,
            fan: $fan,
            summary: $summary,
        );
    }
}
