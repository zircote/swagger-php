<?php

/**
 * @license Apache 2.0
 */

namespace Swagger\Annotations;

/**
 * @Annotation
 */
class DynamicDefinition extends Schema
{
    /**
     * The key into Swagger->definitions array.
     * @var string
     */
    public $definition;

    /** @inheritdoc */
    public static $_types = [
        'definition' => 'string'
    ];

    /** @inheritdoc */
    public static $_parents = [
        'Swagger\Annotations\Swagger'
    ];
}
