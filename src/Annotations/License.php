<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace Swagger\Annotations;

/**
 * @Annotation
 * License information for the exposed API.
 *
 * A "License Object": https://github.com/OAI/OpenAPI-Specification/blob/OpenAPI.next/versions/3.0.md#licenseObject
 */
class License extends AbstractAnnotation
{
    /**
     * The license name used for the API.
     * @var string
     */
    public $name;

    /**
     * A URL to the license used for the API.
     * @var string
     */
    public $url;

    /** @inheritdoc */
    public static $_types = [
        'name' => 'string',
        'url' => 'string',
    ];

    /** @inheritdoc */
    public static $_required = ['name'];

    /** @inheritdoc */
    public static $_parents = [
        'Swagger\Annotations\Info'
    ];
}
