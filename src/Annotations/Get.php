<?php

/**
 * @license Apache 2.0
 */

namespace Swagger\Annotations;

/**
 * @Annotation
 */
class Get extends Operation {

    public $method = 'get';

    public static $parents = [
        'Swagger\Annotations\Path',
        'Swagger\Annotations\Swagger'
    ];
}
