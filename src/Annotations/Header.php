<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace Swagger\Annotations;

/**
 * @Annotation
 *
 * A "Header Object" https://github.com/OAI/OpenAPI-Specification/blob/OpenAPI.next/versions/3.0.md#headerObject
 */
class Header extends AbstractAnnotation
{
    /**
     * @var string
     */
    public $header;

    /**
     * @var string
     */
    public $description;

    /**
     * Schema object
     *
     * @var \Swagger\Annotations\Schema
     */
    public $schema;

    /** @inheritdoc */
    public static $_required = ['header', 'schema'];

    /** @inheritdoc */
    public static $_types = [
        'header' => 'string',
        'description' => 'string',
    ];

    /** @inheritdoc */
    public static $_nested = [
        'Swagger\Annotations\Schema' => 'schema'
    ];

    /** @inheritdoc */
    public static $_parents = [
        'Swagger\Annotations\Components',
        'Swagger\Annotations\Response'
    ];
}
