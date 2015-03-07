<?php

/**
 * @license Apache 2.0
 */

namespace Swagger\Annotations;

/**
 * @Annotation
 */
class Delete extends Operation {

    public $method = 'delete';

    public static $_parents = [
        'Swagger\Annotations\Path',
        'Swagger\Annotations\Swagger'
    ];
}
