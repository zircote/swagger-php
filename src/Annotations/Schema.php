<?php declare(strict_types=1);

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
 * A "Schema Object": https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.0.md#schemaObject
 * JSON Schema: http://json-schema.org/
 */
class Schema extends AbstractAnnotation
{
    /**
     * $ref See http://json-schema.org/latest/json-schema-core.html#rfc.section.7
     * @var string
     */
    public $ref;

    /**
     * The key into Components->schemas array.
     * @var string
     */
    public $schema;

    /**
     * Can be used to decorate a user interface with information about the data produced by this user interface. preferrably be short.
     * @var string
     */
    public $title;

    /**
     * A description will provide explanation about the purpose of the instance described by this schema.
     * @var string
     */
    public $description;

    /**
     * An object instance is valid against "maxProperties" if its number of properties is less than, or equal to, the value of this property.
     * @var integer
     */
    public $maxProperties;

    /**
     * An object instance is valid against "minProperties" if its number of properties is greater than, or equal to, the value of this property.
     * @var integer
     */
    public $minProperties;

    /**
     * An object instance is valid against this property if its property set contains all elements in this property's array value.
     * @var string[]
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
     * @var Items
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
     * A string instance is considered valid if the regular expression matches the instance successfully.
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
     * A numeric instance is valid against "multipleOf" if the result of the division of the instance by this property's value is an integer.
     * @var number
     */
    public $multipleOf;

    /**
     * Adds support for polymorphism.
     * The discriminator is an object name that is used to differentiate between other schemas which may satisfy the payload description.
     * See Composition and Inheritance for more details.
     */
    public $discriminator;

    /**
     * Relevant only for Schema "properties" definitions.
     * Declares the property as "read only".
     * This means that it may be sent as part of a response but should not be sent as part of the request.
     * If the property is marked as readOnly being true and is in the required list, the required will take effect on the response only.
     * A property must not be marked as both readOnly and writeOnly being true.
     * Default value is false.
     * @var boolean
     */
    public $readOnly;

    /**
     * Relevant only for Schema "properties" definitions.
     * Declares the property as "write only".
     * Therefore, it may be sent as part of a request but should not be sent as part of the response.
     * If the property is marked as writeOnly being true and is in the required list, the required will take effect on the request only.
     * A property must not be marked as both readOnly and writeOnly being true.
     * Default value is false.
     * @var boolean
     */
    public $writeOnly;

    /**
     * This may be used only on properties schemas.
     * It has no effect on root schemas.
     * Adds additional metadata to describe the XML representation of this property.
     * @var Xml
     */
    public $xml;

    /**
     * Additional external documentation for this schema.
     * @var ExternalDocumentation
     */
    public $externalDocs;

    /**
     * A free-form property to include an example of an instance for this schema.
     * To represent examples that cannot be naturally represented in JSON or YAML, a string value can be used to contain the example with escaping where necessary.
     */
    public $example;

    /**
     * Allows sending a null value for the defined schema.
     * Default value is false.
     * @var boolean
     */
    public $nullable;

    /**
    * Specifies that a schema is deprecated and should be transitioned out of usage.
    * Default value is false.
    * @var boolean
    */
    public $deprecated;

    /**
     * An instance validates successfully against this property if it validates successfully against all schemas defined by this property's value.
     * @var Schema[]
     */
    public $allOf;

    /**
     * An instance validates successfully against this property if it validates successfully against at least one schema defined by this property's value.
     * @var Schema[]
     */
    public $anyOf;

    /**
     * An instance validates successfully against this property if it validates successfully against exactly one schema defined by this property's value.
     * @var type
     */
    public $oneOf;

    /**
     * http://json-schema.org/latest/json-schema-validation.html#rfc.section.6.29
     */
    public $not;

    /**
     * http://json-schema.org/latest/json-schema-validation.html#anchor64
     * @var bool|object
     */
    public $additionalProperties;

    /**
     * http://json-schema.org/latest/json-schema-validation.html#rfc.section.6.10
     */
    public $additionalItems;

    /**
     * http://json-schema.org/latest/json-schema-validation.html#rfc.section.6.14
     */
    public $contains;

    /**
     * http://json-schema.org/latest/json-schema-validation.html#rfc.section.6.19
     */
    public $patternProperties;

    /**
     * http://json-schema.org/latest/json-schema-validation.html#rfc.section.6.21
     */
    public $dependencies;

    /**
     * http://json-schema.org/latest/json-schema-validation.html#rfc.section.6.22
     */
    public $propertyNames;

    /**
     * http://json-schema.org/latest/json-schema-validation.html#rfc.section.6.24
     */
    public $const;

    /** @inheritdoc */
    public static $_types = [
        'description' => 'string',
        'required' => '[string]',
        'format' => 'string',
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
        'Swagger\Annotations\Property' => ['properties', 'property'],
        'Swagger\Annotations\ExternalDocumentation' => 'externalDocs',
        'Swagger\Annotations\Xml' => 'xml',
        'Swagger\Annotations\AdditionalProperties' => 'additionalProperties'
    ];

    /** @inheritdoc */
    public static $_parents = [
        'Swagger\Annotations\Components',
        'Swagger\Annotations\Parameter',
        'Swagger\Annotations\MediaType',
        'Swagger\Annotations\Header',
    ];
}
