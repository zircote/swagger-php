<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Annotations as OA;
use OpenApi\Generator;

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
     * @var Encoding[]
     */
    public $encoding = Generator::UNDEFINED;

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
