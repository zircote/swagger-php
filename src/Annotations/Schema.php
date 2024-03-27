<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Generator;

/**
 * The definition of input and output data types.
 *
 * These types can be objects, but also primitives and arrays.
 *
 * This object is based on the [JSON Schema Specification](http://json-schema.org) and uses a predefined subset of it.
 * On top of this subset, there are extensions provided by this specification to allow for more complete documentation.
 *
 * @see [OAI Schema Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#schemaObject)
 * @see [JSON Schema](http://json-schema.org/)
 *
 * @Annotation
 */
class Schema extends AbstractAnnotation
{
    /**
     * The relative or absolute path to the endpoint.
     *
     * @see [Using refs](https://swagger.io/docs/specification/using-ref/)
     *
     * @var string|class-string|object
     */
    public $ref = Generator::UNDEFINED;

    /**
     * The key into Components->schemas array.
     *
     * @var string
     */
    public $schema = Generator::UNDEFINED;

    /**
     * Can be used to decorate a user interface with information about the data produced by this user interface.
     *
     * Preferably short; use <code>description</code> for more details.
     *
     * @var string
     */
    public $title = Generator::UNDEFINED;

    /**
     * A description will provide explanation about the purpose of the instance described by this schema.
     *
     * @var string
     */
    public $description = Generator::UNDEFINED;

    /**
     * The maximum number of properties allowed in an object instance.
     * An object instance is valid against this property if its number of properties is less than, or equal to, the
     * value of this attribute.
     *
     * @var int
     */
    public $maxProperties = Generator::UNDEFINED;

    /**
     * The minimum number of properties allowed in an object instance.
     * An object instance is valid against this property if its number of properties is greater than, or equal to, the
     * value of this attribute.
     *
     * @var int
     */
    public $minProperties = Generator::UNDEFINED;

    /**
     * An object instance is valid against this property if its property set contains all elements in this property's
     * array value.
     *
     * @var string[]
     */
    public $required = Generator::UNDEFINED;

    /**
     * A collection of properties to define for an object.
     *
     * Each property is represented as an instance of the <a href="#property">Property</a> class.
     *
     * @var Property[]
     */
    public $properties = Generator::UNDEFINED;

    /**
     * The type of the schema/property.
     *
     * OpenApi v3.0: The value MUST be one of "string", "number", "integer", "boolean", "array" or "object".
     *
     * Since OpenApi v3.1 an array of types may be used.
     *
     * @var string|non-empty-array<string>
     */
    public $type = Generator::UNDEFINED;

    /**
     * The extending format for the previously mentioned type. See Data Type Formats for further details.
     *
     * @var string
     */
    public $format = Generator::UNDEFINED;

    /**
     * Required if type is "array". Describes the type of items in the array.
     *
     * @var Items
     */
    public $items = Generator::UNDEFINED;

    /**
     * Determines the format of the array if type array is used.
     *
     * Possible values are:
     * - csv: comma separated values foo,bar.
     * - ssv: space separated values foo bar.
     * - tsv: tab separated values foo\tbar.
     * - pipes: pipe separated values foo|bar.
     * - multi: corresponds to multiple parameter instances instead of multiple values for a single instance
     * foo=bar&foo=baz. This is valid only for parameters of type <code>query</code> or <code>formData</code>. Default
     * value is csv.
     *
     * @var string
     */
    public $collectionFormat = Generator::UNDEFINED;

    /**
     * Sets a default value to the parameter. The type of the value depends on the defined type.
     *
     * @see [JSON schema validation](http://json-schema.org/latest/json-schema-validation.html#anchor101)
     */
    public $default = Generator::UNDEFINED;

    /**
     * The maximum value allowed for a numeric property. This value must be a number.
     *
     * @see [JSON schema validation](http://json-schema.org/latest/json-schema-validation.html#anchor17)
     *
     * @var int|float
     */
    public $maximum = Generator::UNDEFINED;

    /**
     * A boolean indicating whether the maximum value is excluded from the set of valid values.
     *
     * When set to true, the maximum value is excluded, and when false or not specified, it is included.
     *
     * @see [JSON schema validation](http://json-schema.org/latest/json-schema-validation.html#anchor17)
     *
     * @var bool|int|float
     */
    public $exclusiveMaximum = Generator::UNDEFINED;

    /**
     * The minimum value allowed for a numeric property. This value must be a number.
     *
     * @see [JSON schema validation](http://json-schema.org/latest/json-schema-validation.html#anchor21)
     *
     * @var int|float
     */
    public $minimum = Generator::UNDEFINED;

    /**
     * A boolean indicating whether the minimum value is excluded from the set of valid values.
     *
     * When set to true, the minimum value is excluded, and when false or not specified, it is included.
     *
     * @see [JSON schema validation](http://json-schema.org/latest/json-schema-validation.html#anchor21)
     *
     * @var bool|int|float
     */
    public $exclusiveMinimum = Generator::UNDEFINED;

    /**
     * The maximum length of a string property.
     *
     * A string instance is valid against this property if its length is less than, or equal to, the value of this
     * attribute.
     *
     * @see [JSON schema validation](http://json-schema.org/latest/json-schema-validation.html#anchor26)
     *
     * @var int
     */
    public $maxLength = Generator::UNDEFINED;

    /**
     * The minimum length of a string property.
     *
     * A string instance is valid against this property if its length is greater than, or equal to, the value of this
     * attribute.
     *
     * @see [JSON schema validation](http://json-schema.org/latest/json-schema-validation.html#anchor29)
     *
     * @var int
     */
    public $minLength = Generator::UNDEFINED;

    /**
     * A string instance is considered valid if the regular expression matches the instance successfully.
     *
     * @var string
     */
    public $pattern = Generator::UNDEFINED;

    /**
     * The maximum number of items allowed in an array property.
     *
     * An array instance is valid against this property if its number of items is less than, or equal to, the value of
     * this attribute.
     *
     * @see [JSON schema validation](http://json-schema.org/latest/json-schema-validation.html#anchor42)
     *
     * @var int
     */
    public $maxItems = Generator::UNDEFINED;

    /**
     * The minimum number of items allowed in an array property.
     *
     * An array instance is valid against this property if its number of items is greater than, or equal to, the value
     * of this attribute.
     *
     * @see [JSON schema validation](http://json-schema.org/latest/json-schema-validation.html#anchor45)
     *
     * @var int
     */
    public $minItems = Generator::UNDEFINED;

    /**
     * A boolean value indicating whether all items in an array property must be unique.
     *
     * If this attribute is set to true, then all items in the array must be unique.
     *
     * @see [JSON schema validation](http://json-schema.org/latest/json-schema-validation.html#anchor49)
     *
     * @var bool
     */
    public $uniqueItems = Generator::UNDEFINED;

    /**
     * A collection of allowable values for a property.
     *
     * A property instance is valid against this attribute if its value is one of the values specified in this
     * collection.
     *
     * @see [JSON schema validation](http://json-schema.org/latest/json-schema-validation.html#anchor76)
     *
     * @var array<string|int|float|bool|\UnitEnum>|class-string
     */
    public $enum = Generator::UNDEFINED;

    /**
     * A numeric instance is valid against "multipleOf" if the result of the division of the instance by this
     * property's value is an integer.
     *
     * @var int|float
     */
    public $multipleOf = Generator::UNDEFINED;

    /**
     * Adds support for polymorphism.
     *
     * The discriminator is an object name that is used to differentiate between other schemas which may satisfy the
     * payload description. See Composition and Inheritance for more details.
     *
     * @var Discriminator
     */
    public $discriminator = Generator::UNDEFINED;

    /**
     * Declares the property as "read only".
     *
     * Relevant only for Schema "properties" definitions.
     *
     * This means that it may be sent as part of a response but should not be sent as part of the request.
     * If the property is marked as readOnly being true and is in the required list, the required will take effect on
     * the response only. A property must not be marked as both readOnly and writeOnly being true. Default value is
     * false.
     *
     * @var bool
     */
    public $readOnly = Generator::UNDEFINED;

    /**
     * Declares the property as "write only".
     *
     * Relevant only for Schema "properties" definitions.
     * Therefore, it may be sent as part of a request but should not be sent as part of the response.
     * If the property is marked as writeOnly being true and is in the required list, the required will take effect on
     * the request only. A property must not be marked as both readOnly and writeOnly being true. Default value is
     * false.
     *
     * @var bool
     */
    public $writeOnly = Generator::UNDEFINED;

    /**
     * This may be used only on properties schemas.
     *
     * It has no effect on root schemas.
     * Adds additional metadata to describe the XML representation of this property.
     *
     * @var Xml
     */
    public $xml = Generator::UNDEFINED;

    /**
     * Additional external documentation for this schema.
     *
     * @var ExternalDocumentation
     */
    public $externalDocs = Generator::UNDEFINED;

    /**
     * A free-form property to include an example of an instance for this schema.
     *
     * To represent examples that cannot naturally be represented in JSON or YAML, a string value can be used to
     * contain the example with escaping where necessary.
     */
    public $example = Generator::UNDEFINED;

    /**
     * Examples of the schema.
     *
     * Each example should contain a value in the correct format as specified in the parameter encoding.
     * The examples object is mutually exclusive of the example object.
     * Furthermore, if referencing a schema which contains an example, the examples value shall override the example provided by the schema.
     *
     * @since 3.1.0
     *
     * @var array<Examples>
     */
    public $examples = Generator::UNDEFINED;

    /**
     * Allows sending a null value for the defined schema.
     * Default value is false.
     *
     * This must not be used when using OpenApi version 3.1,
     * instead make the "type" property an array and add "null" as a possible type.
     *
     * @var bool
     *
     * @see https://www.openapis.org/blog/2021/02/16/migrating-from-openapi-3-0-to-3-1-0
     */
    public $nullable = Generator::UNDEFINED;

    /**
     * Specifies that a schema is deprecated and should be transitioned out of usage.
     * Default value is false.
     *
     * @var bool
     */
    public $deprecated = Generator::UNDEFINED;

    /**
     * An instance validates successfully against this property if it validates successfully against all schemas
     * defined by this property's value.
     *
     * @var array<Schema|\OpenApi\Attributes\Schema>
     */
    public $allOf = Generator::UNDEFINED;

    /**
     * An instance validates successfully against this property if it validates successfully against at least one
     * schema defined by this property's value.
     *
     * @var array<Schema|\OpenApi\Attributes\Schema>
     */
    public $anyOf = Generator::UNDEFINED;

    /**
     * An instance validates successfully against this property if it validates successfully against exactly one schema
     * defined by this property's value.
     *
     * @var array<Schema|\OpenApi\Attributes\Schema>
     */
    public $oneOf = Generator::UNDEFINED;

    /**
     * http://json-schema.org/latest/json-schema-validation.html#rfc.section.6.29.
     */
    public $not = Generator::UNDEFINED;

    /**
     * http://json-schema.org/latest/json-schema-validation.html#anchor64.
     *
     * @var bool|AdditionalProperties
     */
    public $additionalProperties = Generator::UNDEFINED;

    /**
     * http://json-schema.org/latest/json-schema-validation.html#rfc.section.6.10.
     */
    public $additionalItems = Generator::UNDEFINED;

    /**
     * http://json-schema.org/latest/json-schema-validation.html#rfc.section.6.14.
     */
    public $contains = Generator::UNDEFINED;

    /**
     * http://json-schema.org/latest/json-schema-validation.html#rfc.section.6.19.
     */
    public $patternProperties = Generator::UNDEFINED;

    /**
     * http://json-schema.org/latest/json-schema-validation.html#rfc.section.6.21.
     */
    public $dependencies = Generator::UNDEFINED;

    /**
     * http://json-schema.org/latest/json-schema-validation.html#rfc.section.6.22.
     */
    public $propertyNames = Generator::UNDEFINED;

    /**
     * http://json-schema.org/draft/2020-12/json-schema-validation.html#rfc.section.6.1.3.
     */
    public $const = Generator::UNDEFINED;

    /**
     * @inheritdoc
     */
    public static $_types = [
        'title' => 'string',
        'description' => 'string',
        'required' => '[string]',
        'format' => 'string',
        'collectionFormat' => ['csv', 'ssv', 'tsv', 'pipes', 'multi'],
        'maximum' => 'number',
        'exclusiveMaximum' => 'boolean|integer|number',
        'minimum' => 'number',
        'exclusiveMinimum' => 'boolean|integer|number',
        'maxLength' => 'integer',
        'minLength' => 'integer',
        'pattern' => 'string',
        'maxItems' => 'integer',
        'minItems' => 'integer',
        'uniqueItems' => 'boolean',
        'multipleOf' => 'integer',
        'allOf' => '[' . Schema::class . ']',
        'oneOf' => '[' . Schema::class . ']',
        'anyOf' => '[' . Schema::class . ']',
    ];

    /**
     * @inheritdoc
     */
    public static $_nested = [
        Discriminator::class => 'discriminator',
        Items::class => 'items',
        Property::class => ['properties', 'property'],
        ExternalDocumentation::class => 'externalDocs',
        Examples::class => ['examples', 'example'],
        Xml::class => 'xml',
        AdditionalProperties::class => 'additionalProperties',
        Attachable::class => ['attachables'],
    ];

    /**
     * @inheritdoc
     */
    public static $_parents = [
        Components::class,
        Parameter::class,
        PathParameter::class,
        MediaType::class,
        Header::class,
    ];

    /**
     * @inheritdoc
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $data = parent::jsonSerialize();

        if ($this->_context->isVersion(OpenApi::VERSION_3_0_0)) {
            unset($data->examples);
            if (isset($data->const)) {
                $data->enum = [$data->const];
                unset($data->const);
            }
        }

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function validate(array $stack = [], array $skip = [], string $ref = '', $context = null): bool
    {
        if ($this->type === 'array' && Generator::isDefault($this->items)) {
            $this->_context->logger->warning('@OA\\Items() is required when ' . $this->identity() . ' has type "array" in ' . $this->_context);

            return false;
        }

        if ($this->_context->isVersion(OpenApi::VERSION_3_0_0)) {
            if (!Generator::isDefault($this->examples)) {
                $this->_context->logger->warning($this->identity() . ' is only allowed for ' . OpenApi::VERSION_3_1_0);

                return false;
            }
        }

        return parent::validate($stack, $skip, $ref, $context);
    }
}
