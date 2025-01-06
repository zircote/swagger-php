<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Generator;

/**
 * Each Media Type object provides schema and examples for the media type identified by its key.
 *
 * @see [Media Type Object](https://spec.openapis.org/oas/v3.1.1.html#media-type-object)
 *
 * @Annotation
 */
class MediaType extends AbstractAnnotation
{
    /**
     * The key into Operation->content array.
     *
     * @var string
     */
    public $mediaType = Generator::UNDEFINED;

    /**
     * The schema defining the type used for the request body.
     *
     * @var Schema
     */
    public $schema = Generator::UNDEFINED;

    /**
     * Example of the media type.
     *
     * The example object should be in the correct format as specified by the media type.
     * The example object is mutually exclusive of the examples object.
     *
     * Furthermore, if referencing a schema which contains an example,
     * the example value shall override the example provided by the schema.
     */
    public $example = Generator::UNDEFINED;

    /**
     * Examples of the media type.
     *
     * Each example should contain a value in the correct format as specified in the parameter encoding.
     * The examples object is mutually exclusive of the example object.
     * Furthermore, if referencing a schema which contains an example, the examples value shall override the example provided by the schema.
     *
     * @var array<Examples>
     */
    public $examples = Generator::UNDEFINED;

    /**
     * A map between a property name and its encoding information.
     *
     * The key, being the property name, must exist in the schema as a property.
     *
     * The encoding object shall only apply to requestBody objects when the media type is multipart or
     * application/x-www-form-urlencoded.
     *
     * @var array<string,mixed>
     */
    public $encoding = Generator::UNDEFINED;

    /**
     * @inheritdoc
     */
    public static $_nested = [
        Schema::class => 'schema',
        Examples::class => ['examples', 'example'],
        Attachable::class => ['attachables'],
    ];

    /**
     * @inheritdoc
     */
    public static $_parents = [
        Response::class,
        RequestBody::class,
    ];
}
