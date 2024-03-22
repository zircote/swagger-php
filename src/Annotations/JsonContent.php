<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Annotations as OA;

/**
 * Shorthand for a json response.
 *
 * Use as `@OA\Schema` inside a `Response` and `MediaType`->`'application/json'` will be generated.
 *
 * @Annotation
 */
class JsonContent extends Schema
{
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
        Examples::class => ['examples', 'example'],
        Attachable::class => ['attachables'],
    ];
}
