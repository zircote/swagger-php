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
class Parameter extends AbstractAnnotation {

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

    /**
     * The type of the parameter. Since the parameter is not located at the request body, it is limited to simple types (that is, not an object). The value MUST be one of "string", "number", "integer", "boolean", "array" or "file". If type is "file", the consumes MUST be either "multipart/form-data" or " application/x-www-form-urlencoded" and the parameter MUST be in "formData".
     * @var string
     */
    public $type;

    /**
     * The extending format for the previously mentioned type. See Data Type Formats for further details.
     * @var string
     */
    public $format;

    /**
     * Required if type is "array". Describes the type of items in the array.
     * @var array
     */
    public $items;

    /**
     * @var string Determines the format of the array if type array is used. Possible values are: csv - comma separated values foo,bar. ssv - space separated values foo bar. tsv - tab separated values foo\tbar. pipes - pipe separated values foo|bar. multi - corresponds to multiple parameter instances instead of multiple values for a single instance foo=bar&foo=baz. This is valid only for parameters in "query" or "formData". Default value is csv.
     */
    public $collectionFormat;

    /**
     * Sets a default value to the parameter. The type of the value depends on the defined type. See http://json-schema.org/latest/json-schema-validation.html#anchor101.
     * @var mixed
     */
    public $default = AbstractAnnotation::UNDEFINED;

    /**
     * See http://json-schema.org/latest/json-schema-validation.html#anchor17.
     * @var number
     */
    public $maximum;

    /**
     * See http://json-schema.org/latest/json-schema-validation.html#anchor17.
     * @var boolean
     */
    public $exclusiveMaximum;

    /**
     * See http://json-schema.org/latest/json-schema-validation.html#anchor21.
     * @var number
     */
    public $minimum;

    /**
     * See http://json-schema.org/latest/json-schema-validation.html#anchor21.
     * @var boolean
     */
    public $exclusiveMinimum;

    /**
     * See http://json-schema.org/latest/json-schema-validation.html#anchor26.
     * @var integer
     */
    public $maxLength;

    /**
     * See http://json-schema.org/latest/json-schema-validation.html#anchor29.
     * @var integer
     */
    public $minLength;

    /**
     * See http://json-schema.org/latest/json-schema-validation.html#anchor33.
     * @var string
     */
    public $pattern;

    /**
     * See http://json-schema.org/latest/json-schema-validation.html#anchor42.
     * @var integer
     */
    public $maxItems;

    /**
     * See http://json-schema.org/latest/json-schema-validation.html#anchor45.
     * @var integer
     */
    public $minItems;

    /**
     * See http://json-schema.org/latest/json-schema-validation.html#anchor49.
     * @var boolean
     */
    public $uniqueItems;

    /**
     * See http://json-schema.org/latest/json-schema-validation.html#anchor76.
     * @var array
     */
    public $enum;

    /**
     * See http://json-schema.org/latest/json-schema-validation.html#anchor14.
     * @var number
     */
    public $multipleOf;

    public static $nested = [
        'Swagger\Annotations\Items' =>'items'
    ];
    public static $parents = [
        'Swagger\Annotations\Operation',
        'Swagger\Annotations\Get',
        'Swagger\Annotations\Post',
        'Swagger\Annotations\Put',
        'Swagger\Annotations\Delete',
        'Swagger\Annotations\Path',
    ];

}
