<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace Swagger\Annotations;

/**
 * @Annotation
 * A "Response Object": https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#requestBodyObject
 *
 * Describes a single response from an API Operation, including design-time, static links to operations based on the
 * response.
 */
class RequestBody extends AbstractAnnotation
{
    public $ref;

    /**
     * Request body model name.
     *
     * @var string
     */
    public $request;

    /**
     * A brief description of the parameter.
     * This could contain examples of use.
     * CommonMark syntax may be used for rich text representation.
     *
     * @var string
     */
    public $description;

    /**
     * Determines whether this parameter is mandatory.
     * If the parameter location is "path", this property is required and its value must be true.
     * Otherwise, the property may be included and its default value is false
     *
     * @var boolean
     */
    public $required;

    /**
     * A map containing descriptions of potential response payloads.
     * The key is a media type or media type range and the value describes it.
     * For responses that match multiple keys, only the most specific key is applicable. e.g. text/plain overrides
     * text/*
     *
     * @var MediaType[]
     */
    public $content;

    /** @inheritdoc */
    public static $_types = [
        'description' => 'string',
        'required'    => 'boolean',
        'request'     => 'string',
    ];

    public static $_parents = [
        'Swagger\Annotations\Components',
        'Swagger\Annotations\Delete',
        'Swagger\Annotations\Get',
        'Swagger\Annotations\Head',
        'Swagger\Annotations\Operation',
        'Swagger\Annotations\Options',
        'Swagger\Annotations\Patch',
        'Swagger\Annotations\Post',
        'Swagger\Annotations\Trace',
        'Swagger\Annotations\Put',
    ];

    /** @inheritdoc */
    public static $_nested = [
        'Swagger\Annotations\MediaType' => ['content', 'mediaType'],
    ];
}
