<?php

/**
 * @license Apache 2.0
 */

namespace Swagger\Annotations;

/**
 * @Annotation
 */
class Definition extends Schema
{
    /**
     * The key in the OpenApi->definitions array.
     * @var string
     */
    public $definition;

    /** @inheritdoc */
    public static $_types = [
        'definition' => 'string'
    ];

    /** @inheritdoc */
    public static $_parents = [
        'Swagger\Annotations\OpenApi'
    ];
}
