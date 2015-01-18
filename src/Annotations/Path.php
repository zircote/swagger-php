<?php

/**
 * @license Apache 2.0
 */

namespace Swagger\Annotations;

/**
 * @Annotation
 */
class Path extends AbstractAnnotation {

    /**
     * key in the Swagger "Paths Object" for this path.
     * @var string
     */
    public $path;

    /**
     * A definition of a GET operation on this path.
     * @var Get|Operation
     */
    public $get;

    /**
     * A definition of a PUT operation on this path.
     * @var Operation
     */
    public $put;

    /**
     * A definition of a POST operation on this path.
     * @var Operation
     */
    public $post;

    /**
     * A definition of a DELETE operation on this path.
     * @var Operation
     */
    public $delete;

    /**
     * A definition of a OPTIONS operation on this path.
     * @var Operation
     */
    public $options;

    /**
     * A definition of a HEAD operation on this path.
     * @var Operation
     */
    public $head;

    /**
     * A definition of a PATCH operation on this path.
     * @var Operation
     */
    public $patch;

    /**
     * A list of parameters that are applicable for all the operations described under this path. These parameters can be overridden at the operation level, but cannot be removed there. The list MUST NOT include duplicated parameters. A unique parameter is defined by a combination of a name and location. The list can use the Reference Object to link to parameters that are defined at the Swagger Object's parameters. There can be one "body" parameter at most.
     * @var Parameter[]
     */
    public $parameters;

    public static $nested = [
        'Swagger\Annotations\Get' => 'get',
        'Swagger\Annotations\Post' => 'post',
        'Swagger\Annotations\Put' => 'put',
        'Swagger\Annotations\Delete' => 'delete',
    ];

    public function jsonSerialize() {
        $data = parent::jsonSerialize();
        unset($data['path']);
        return $data;
    }
}
