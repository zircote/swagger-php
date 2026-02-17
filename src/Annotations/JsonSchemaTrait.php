<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Generator;

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
    public $default = Generator::UNDEFINED;

    /**
     * The maximum value allowed for a numeric property. This value must be a number.
     *
     * @var int|float
     */
    public $maximum = Generator::UNDEFINED;

    /**
     * A boolean indicating whether the maximum value is excluded from the set of valid values.
     *
     * When set to true, the maximum value is excluded, and when false or not specified, it is included.
     *
     * @var bool|int|float
     */
    public $exclusiveMaximum = Generator::UNDEFINED;

    /**
     * The minimum value allowed for a numeric property. This value must be a number.
     *
     * @var int|float
     */
    public $minimum = Generator::UNDEFINED;

    /**
     * A boolean indicating whether the minimum value is excluded from the set of valid values.
     *
     * When set to true, the minimum value is excluded, and when false or not specified, it is included.
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
     * @var int
     */
    public $maxLength = Generator::UNDEFINED;

    /**
     * The minimum length of a string property.
     *
     * A string instance is valid against this property if its length is greater than, or equal to, the value of this
     * attribute.
     *
     * @var int
     */
    public $minLength = Generator::UNDEFINED;

    /**
     * The maximum number of items allowed in an array property.
     *
     * An array instance is valid against this property if its number of items is less than, or equal to, the value of
     * this attribute.
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
     * @var int
     */
    public $minItems = Generator::UNDEFINED;

    /**
     * A boolean value indicating whether all items in an array property must be unique.
     *
     * If this attribute is set to true, then all items in the array must be unique.
     *
     * @var bool
     */
    public $uniqueItems = Generator::UNDEFINED;

    /**
     * A list of allowable values for a property.
     *
     * A property instance is valid against this attribute if its value is one of the values specified in this
     * list.
     *
     * @var list<string|int|float|bool|\UnitEnum>|class-string
     */
    public $enum = Generator::UNDEFINED;

    /**
     * @var mixed
     */
    public $not = Generator::UNDEFINED;

    /**
     * @var bool|AdditionalProperties
     */
    public $additionalProperties = Generator::UNDEFINED;

    /**
     * @var array
     */
    public $additionalItems = Generator::UNDEFINED;

    /**
     * @var array
     */
    public $contains = Generator::UNDEFINED;

    /**
     * @var array
     */
    public $patternProperties = Generator::UNDEFINED;

    /**
     * @var array
     */
    public $unevaluatedProperties = Generator::UNDEFINED;

    /**
     * @var mixed
     */
    public $dependencies = Generator::UNDEFINED;

    /**
     * @var mixed
     */
    public $propertyNames = Generator::UNDEFINED;

    /**
     * @var mixed
     * @since OpenAPI 3.1.0
     */
    public $const = Generator::UNDEFINED;
}

/*
** TYPE-HINTS:

     * @param list<string|int|float|bool|\UnitEnum|null>|class-string|null $enum


** PARAMETERS:

        // JSON Schema
        mixed $default = Generator::UNDEFINED,
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
        mixed $not = Generator::UNDEFINED,
        bool|AdditionalProperties|null $additionalProperties = null,
        array|null $additionalItems = null,
        array|null $contains = null,
        array|null $patternProperties = null,
        array|null $unevaluatedProperties = null,
        mixed $dependencies = Generator::UNDEFINED,
        mixed $propertyNames = Generator::UNDEFINED,
        mixed $const = Generator::UNDEFINED,


** PARENT-PARAMS:

            // JSON Schema
            'default' => $default,
            'maximum' => $maximum ?? Generator::UNDEFINED,
            'exclusiveMaximum' => $exclusiveMaximum ?? Generator::UNDEFINED,
            'minimum' => $minimum ?? Generator::UNDEFINED,
            'exclusiveMinimum' => $exclusiveMinimum ?? Generator::UNDEFINED,
            'maxLength' => $maxLength ?? Generator::UNDEFINED,
            'minLength' => $minLength ?? Generator::UNDEFINED,
            'maxItems' => $maxItems ?? Generator::UNDEFINED,
            'minItems' => $minItems ?? Generator::UNDEFINED,
            'uniqueItems' => $uniqueItems ?? Generator::UNDEFINED,
            'enum' => $enum ?? Generator::UNDEFINED,
            'not' => $not,
            'additionalProperties' => $additionalProperties ?? Generator::UNDEFINED,
            'additionalItems' => $additionalItems ?? Generator::UNDEFINED,
            'contains' => $contains ?? Generator::UNDEFINED,
            'patternProperties' => $patternProperties ?? Generator::UNDEFINED,
            'unevaluatedProperties' => $unevaluatedProperties ?? Generator::UNDEFINED,
            'dependencies' => $dependencies,
            'propertyNames' => $propertyNames,
            'const' => $const,

*/
