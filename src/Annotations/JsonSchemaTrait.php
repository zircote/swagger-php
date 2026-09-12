<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Undefined;

/**
 * Sets a default value to the parameter. The type of the value depends on the defined type.
 *
 * @see [JSON schema validation](http://json-schema.org/latest/json-schema-validation.html)
 */
trait JsonSchemaTrait
{
    /**
     * Sets a default value to the parameter.
     *
     * The type of the value depends on the defined type.
     *
     * @var mixed
     */
    public $default = Undefined::UNDEFINED;

    /**
     * The maximum value allowed for a numeric property. This value must be a number.
     *
     * @var int|float
     */
    public $maximum = Undefined::UNDEFINED;

    /**
     * A boolean indicating whether the maximum value is excluded from the set of valid values.
     *
     * When set to true, the maximum value is excluded, and when false or not specified, it is included.
     *
     * @var bool|int|float
     */
    public $exclusiveMaximum = Undefined::UNDEFINED;

    /**
     * The minimum value allowed for a numeric property. This value must be a number.
     *
     * @var int|float
     */
    public $minimum = Undefined::UNDEFINED;

    /**
     * A boolean indicating whether the minimum value is excluded from the set of valid values.
     *
     * When set to true, the minimum value is excluded, and when false or not specified, it is included.
     *
     * @var bool|int|float
     */
    public $exclusiveMinimum = Undefined::UNDEFINED;

    /**
     * The maximum length of a string property.
     *
     * A string instance is valid against this property if its length is less than, or equal to, the value of this
     * attribute.
     *
     * @var int
     */
    public $maxLength = Undefined::UNDEFINED;

    /**
     * The minimum length of a string property.
     *
     * A string instance is valid against this property if its length is greater than, or equal to, the value of this
     * attribute.
     *
     * @var int
     */
    public $minLength = Undefined::UNDEFINED;

    /**
     * The maximum number of items allowed in an array property.
     *
     * An array instance is valid against this property if its number of items is less than, or equal to, the value of
     * this attribute.
     *
     * @var int
     */
    public $maxItems = Undefined::UNDEFINED;

    /**
     * The minimum number of items allowed in an array property.
     *
     * An array instance is valid against this property if its number of items is greater than, or equal to, the value
     * of this attribute.
     *
     * @var int
     */
    public $minItems = Undefined::UNDEFINED;

    /**
     * A boolean value indicating whether all items in an array property must be unique.
     *
     * If this attribute is set to true, then all items in the array must be unique.
     *
     * @var bool
     */
    public $uniqueItems = Undefined::UNDEFINED;

    /**
     * A list of allowable values for a property.
     *
     * A property instance is valid against this attribute if its value is one of the values specified in this
     * list.
     *
     * @var list<string|int|float|bool|\UnitEnum>|class-string
     */
    public $enum = Undefined::UNDEFINED;

    /**
     * @var mixed
     */
    public $not = Undefined::UNDEFINED;

    /**
     * @var bool|AdditionalProperties
     */
    public $additionalProperties = Undefined::UNDEFINED;

    /**
     * @var array
     */
    public $additionalItems = Undefined::UNDEFINED;

    /**
     * @var array
     */
    public $contains = Undefined::UNDEFINED;

    /**
     * @var int
     * @since OpenAPI 3.1.0
     */
    public $minContains = Undefined::UNDEFINED;

    /**
     * @var int
     * @since OpenAPI 3.1.0
     */
    public $maxContains = Undefined::UNDEFINED;

    /**
     * @var array
     * @since OpenAPI 3.1.0
     */
    public $prefixItems = Undefined::UNDEFINED;

    /**
     * @var array
     */
    public $patternProperties = Undefined::UNDEFINED;

    /**
     * @var array
     */
    public $unevaluatedProperties = Undefined::UNDEFINED;

    /**
     * @var mixed
     * @since OpenAPI 3.1.0
     */
    public $unevaluatedItems = Undefined::UNDEFINED;

    /**
     * @var mixed
     */
    public $dependencies = Undefined::UNDEFINED;

    /**
     * @var array
     * @since OpenAPI 3.1.0
     */
    public $dependentRequired = Undefined::UNDEFINED;

    /**
     * @var array
     * @since OpenAPI 3.1.0
     */
    public $dependentSchemas = Undefined::UNDEFINED;

    /**
     * @var mixed
     */
    public $propertyNames = Undefined::UNDEFINED;

    /**
     * @var mixed
     * @since OpenAPI 3.1.0
     */
    public $const = Undefined::UNDEFINED;

    /**
     * @var mixed
     * @since OpenAPI 3.1.0
     */
    public $if = Undefined::UNDEFINED;

    /**
     * Applied when `if` succeeds.
     *
     * @var mixed
     * @since OpenAPI 3.1.0
     */
    public $then = Undefined::UNDEFINED;

    /**
     * Applied when `if` fails.
     *
     * @var mixed
     * @since OpenAPI 3.1.0
     */
    public $else = Undefined::UNDEFINED;

    /**
     * Schema for the decoded content described by `contentMediaType`.
     *
     * @var mixed
     * @since OpenAPI 3.1.0
     */
    public $contentSchema = Undefined::UNDEFINED;
}

/*
 * Template code to be used by all attributes extending OA\Schema.
 *
 *
** TYPE-HINTS:

     * @param list<string|int|float|bool|\UnitEnum|null>|class-string|null $enum


** PARAMETERS:

        // JSON Schema
        mixed $default = Undefined::UNDEFINED,
        int|float|null $maximum = null,
        bool|int|float|null $exclusiveMaximum = null,
        int|float|null $minimum = null,
        bool|int|float|null $exclusiveMinimum = null,
        int|null $maxLength = null,
        int|null $minLength = null,
        int|null $maxItems = null,
        int|null $minItems = null,
        bool|null $uniqueItems = null,
        array|string|null $enum = null,
        mixed $not = Undefined::UNDEFINED,
        bool|AdditionalProperties|null $additionalProperties = null,
        array|null $additionalItems = null,
        array|null $contains = null,
        int|null $minContains = null,
        int|null $maxContains = null,
        array|null $prefixItems = null,
        array|null $patternProperties = null,
        array|null $unevaluatedProperties = null,
        mixed $unevaluatedItems = Undefined::UNDEFINED,
        mixed $dependencies = Undefined::UNDEFINED,
        array|null $dependentRequired = null,
        array|null $dependentSchemas = null,
        mixed $propertyNames = Undefined::UNDEFINED,
        mixed $const = Undefined::UNDEFINED,
        mixed $if = Undefined::UNDEFINED,
        mixed $then = Undefined::UNDEFINED,
        mixed $else = Undefined::UNDEFINED,
        mixed $contentSchema = Undefined::UNDEFINED,


** PARENT-PARAMS:

            // JSON Schema
            'default' => $default,
            'maximum' => $maximum ?? Undefined::UNDEFINED,
            'exclusiveMaximum' => $exclusiveMaximum ?? Undefined::UNDEFINED,
            'minimum' => $minimum ?? Undefined::UNDEFINED,
            'exclusiveMinimum' => $exclusiveMinimum ?? Undefined::UNDEFINED,
            'maxLength' => $maxLength ?? Undefined::UNDEFINED,
            'minLength' => $minLength ?? Undefined::UNDEFINED,
            'maxItems' => $maxItems ?? Undefined::UNDEFINED,
            'minItems' => $minItems ?? Undefined::UNDEFINED,
            'uniqueItems' => $uniqueItems ?? Undefined::UNDEFINED,
            'enum' => $enum ?? Undefined::UNDEFINED,
            'not' => $not,
            'additionalProperties' => $additionalProperties ?? Undefined::UNDEFINED,
            'additionalItems' => $additionalItems ?? Undefined::UNDEFINED,
            'contains' => $contains ?? Undefined::UNDEFINED,
            'minContains' => $minContains ?? Undefined::UNDEFINED,
            'maxContains' => $maxContains ?? Undefined::UNDEFINED,
            'prefixItems' => $prefixItems ?? Undefined::UNDEFINED,
            'patternProperties' => $patternProperties ?? Undefined::UNDEFINED,
            'unevaluatedProperties' => $unevaluatedProperties ?? Undefined::UNDEFINED,
            'unevaluatedItems' => $unevaluatedItems,
            'dependencies' => $dependencies,
            'dependentRequired' => $dependentRequired ?? Undefined::UNDEFINED,
            'dependentSchemas' => $dependentSchemas ?? Undefined::UNDEFINED,
            'propertyNames' => $propertyNames,
            'const' => $const,
            'if' => $if,
            'then' => $then,
            'else' => $else,
            'contentSchema' => $contentSchema,

*/
