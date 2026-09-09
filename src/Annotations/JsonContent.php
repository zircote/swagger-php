<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Annotations as OA;
use OpenApi\Undefined;

/**
 * Shorthand for a json response.
 *
 * Example:
 * ```php
 * @OA\JsonContent(
 *     ref="#/components/schemas/user"
 * )
 * ```
 * vs.
 * ```php
 * @OA\MediaType(
 *     mediaType="application/json",
 *     @OA\Schema(
 *         ref="#/components/schemas/user"
 *     )
 * )
 * ```
 *
 * @Annotation
 */
class JsonContent extends Schema
{
    /**
     * A map between a property name and its encoding information.
     *
     * @var list<Encoding>
     */
    public $encoding = Undefined::UNDEFINED;

    /**
     * Examples of the media type.
     *
     * These belong to the generated media type rather than to the schema, so unlike the JSON
     * Schema keyword inherited from <code>Schema</code> they are Example Objects and keep every field.
     *
     * @var array<Examples>
     */
    public $examples = Undefined::UNDEFINED;

    /**
     * @inheritdoc
     */
    public static $_parents = [];

    /**
     * @inheritdoc
     */
    public static $_nested = [
        Discriminator::class => 'discriminator',
        Items::class => 'items',
        Property::class => ['properties', 'property'],
        ExternalDocumentation::class => 'externalDocs',
        AdditionalProperties::class => 'additionalProperties',
        Encoding::class => ['encoding', 'property'],
        Examples::class => ['examples', 'example'],
        Attachable::class => ['attachables'],
    ];
}
