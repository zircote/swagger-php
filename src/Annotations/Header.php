<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace Swagger\Annotations;

/**
 * @Annotation
 *
 * A "Header Object" https://github.com/OAI/OpenAPI-Specification/blob/OpenAPI.next/versions/3.0.md#headerObject
 */
class Header extends AbstractAnnotation
{
    /**
     * @var string
     */
    public $header;

    /**
     * @var string
     */
    public $description;

    /**
     * A brief description of the parameter. This could contain examples of use. CommonMark syntax MAY be used for rich text representation.
     *
     * @var bool
     */
    public $required;

    /**
     * Schema object
     *
     * @var \Swagger\Annotations\Schema
     */
    public $schema;

    /**
     * Specifies that a parameter is deprecated and SHOULD be transitioned out of usage.
     *
     * @var bool
     */
    public $deprecated;

    /**
     * Sets the ability to pass empty-valued parameters.
     * This is valid only for query parameters and allows sending a parameter with an empty value.
     * Default value is false. If style is used, and if behavior is n/a
     * (cannot be serialized), the value of allowEmptyValue SHALL be ignored.
     *
     * @var bool
     */
    public $allowEmptyValue;

    /** @inheritdoc */
    public static $_required = ['header', 'schema'];

    /** @inheritdoc */
    public static $_types = [
        'header' => 'string',
        'description' => 'string',
    ];

    /** @inheritdoc */
    public static $_nested = [
        'Swagger\Annotations\Schema' => 'schema'
    ];

    /** @inheritdoc */
    public static $_parents = [
        'Swagger\Annotations\Components',
        'Swagger\Annotations\Response'
    ];
}
