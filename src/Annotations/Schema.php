<?php

use Swagger\Annotations\AbstractAnnotation;

/**
 * @license Apache 2.0
 */

namespace Swagger\Annotations;

/**
 * @Annotation
 * The definition of input and output data types.
 * These types can be objects, but also primitives and arrays.
 * This object is based on the [JSON Schema Specification](http://json-schema.org) and uses a predefined subset of it.
 * On top of this subset, there are extensions provided by this specification to allow for more complete documentation.
 *
 * A Swagger "Schema Object": https://github.com/swagger-api/swagger-spec/blob/master/versions/2.0.md#schemaObject
 */
class Schema extends AbstractAnnotation {

    /**
     * $ref See http://json-schema.org/latest/json-schema-core.html#rfc.section.7
     * @var string
     */
    public $ref;
    public $title;
    public $description;
    public $maxProperties;
    public $minProperties;

    /**
     * @var array
     */
    public $required;

    /**
     * @var Property[]
     */
    public $properties;

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
    public $default = UNDEFINED;

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

    /**
     * Adds support for polymorphism. The discriminator is the schema property name that is used to differentiate between other schemas that inherit this schema. The property name used MUST be defined at this schema and it MUST be in the required property list. When used, the value MUST be the name of this schema or any schema that inherits it.
     * @var string
     */
    public $discriminator;

    /**
     * Relevant only for Schema "properties" definitions. Declares the property as "read only". This means that it MAY be sent as part of a response but MUST NOT be sent as part of the request. Properties marked as readOnly being true SHOULD NOT be in the required list of the defined schema. Default value is false.
     * @var boolean
     */
    public $readOnly;

    /**
     * This MAY be used only on properties schemas. It has no effect on root schemas. Adds Additional metadata to describe the XML representation format of this property.
     * @var Xml
     */
    public $xml;

    /**
     * Additional external documentation for this schema.
     * @var ExternalDocumentation
     */
    public $externalDocs;

    /**
     * A free-form property to include a an example of an instance for this schema.
     * @var array
     */
    public $example;

    /**
     * http://json-schema.org/latest/json-schema-validation.html#anchor82
     * @var Schema[]
     */
    public $allOf;

    /** @inheritdoc */
    public static $_types = [
        'description' => 'string',
        'required' => '[string]',
        'format' => ['int32', 'int64', 'float', 'double', 'byte', 'date', 'date-time'],
        'collectionFormat' => ['csv', 'ssv', 'tsv', 'pipes', 'multi'],
        'maximum' => 'number',
        'exclusiveMaximum' => 'boolean',
        'minimum' => 'number',
        'exclusiveMinimum' => 'boolean',
        'maxLength' => 'integer',
        'minLength' => 'integer',
        'pattern' => 'string',
        'maxItems' => 'integer',
        'minItems' => 'integer',
        'uniqueItems' => 'boolean',
        'multipleOf' => 'integer',
    ];

    /** @inheritdoc */
    public static $_nested = [
        'Swagger\Annotations\Items' => 'items',
        'Swagger\Annotations\Property' => 'properties[]',
        'Swagger\Annotations\ExternalDocumentation' =>'externalDocs',
        'Swagger\Annotations\Xml' => 'xml',
    ];

    /** @inheritdoc */
    public static $_parents = [
        'Swagger\Annotations\Response',
        'Swagger\Annotations\Parameter',
    ];

}
