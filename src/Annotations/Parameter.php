<?php

/**
 * @license Apache 2.0
 */

namespace Swagger\Annotations;

/**
 * @Annotation
 * Describes a single operation parameter.
 *
 * A Swagger "Parameter Object": https://github.com/swagger-api/swagger-spec/blob/master/versions/2.0.md#parameterObject
 */
class Parameter extends Items {

    /**
     * The name of the parameter. Parameter names are case sensitive. If in is "path", the name field MUST correspond to the associated path segment from the path field in the Paths Object. See Path Templating for further information. For all other cases, the name corresponds to the parameter name used based on the in property.
     * @var string
     */
    public $name;

    /**
     * The location of the parameter. Possible values are "query", "header", "path", "formData" or "body".
     * @var string
     */
    public $in;

    /**
     * A brief description of the parameter. This could contain examples of use. GFM syntax can be used for rich text representation.
     * @var string
     */
    public $description;

    /**
     * Determines whether this parameter is mandatory. If the parameter is in "path", this property is required and its value MUST be true. Otherwise, the property MAY be included and its default value is false.
     * @var boolean
     */
    public $required;

    /**
     * The schema defining the type used for the body parameter.
     * @var array
     */
    public $schema;

    public static $parents = [
        'Swagger\Annotations\Get',
        'Swagger\Annotations\Post',
        'Swagger\Annotations\Put',
        'Swagger\Annotations\Delete',
        'Swagger\Annotations\Path'
    ];

}
