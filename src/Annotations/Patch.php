<?php
/**
 * User: granted
 * Date: 3/4/15
 * Time: 3:01 PM
 */

namespace Swagger\Annotations;

/**
 * @Annotation
 */
class Patch extends Operation {

    public $method = 'post';

    /** @inheritdoc */
    public static $parents = [
        'Swagger\Annotations\Path',
        'Swagger\Annotations\Swagger'
    ];
}