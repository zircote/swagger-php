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
    public static $key = 'name';
    public static $parents = [
        'Swagger\Annotations\Swagger',
    ];
}
