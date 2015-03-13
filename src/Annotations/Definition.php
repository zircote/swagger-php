<?php

/**
 * @license Apache 2.0
 */

namespace Swagger\Annotations;

/**
 * @Annotation
 */
class Definition extends Schema {

    /**
     * The key into Swagger->definitions array.
     * @var string
     */
    public $name;

    /** @inheritdoc */
    public static $_types = [
        'name' => 'string'
    ];

    /** @inheritdoc */
    public static $_key = 'name';

    /** @inheritdoc */
    public static $_parents = [
        'Swagger\Annotations\Swagger',
    ];

}
