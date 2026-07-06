<?php declare(strict_types=1);
/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Undefined;

/**
 * @see [Header Object](https://spec.openapis.org/oas/v3.1.1.html#header-object)
 *
 * @Annotation
 */
class Header extends AbstractAnnotation
{
    /**
     * The relative or absolute path to the endpoint.
     *
     * @see [Reference Object](https://spec.openapis.org/oas/v3.1.1.html#reference-object)
     *
     * @var string|class-string|object
     */
    public $ref = Undefined::UNDEFINED;

    /**
     * @var string
     */
    public $header = Undefined::UNDEFINED;

    /**
     * A brief description of the parameter.
     *
     * This could contain examples of use.
     * CommonMark syntax MAY be used for rich text representation.
     *
     * @var string
     */
    public $description = Undefined::UNDEFINED;

    /**
     * @var bool
     */
    public $required = Undefined::UNDEFINED;

    /**
     * Schema object.
     *
     * @var Schema
     */
    public $schema = Undefined::UNDEFINED;

    /**
     * Specifies that a parameter is deprecated and SHOULD be transitioned out of usage.
     *
     * @var bool
     */
    public $deprecated = Undefined::UNDEFINED;

    /**
     * Sets the ability to pass empty-valued parameters.
     *
     * This is valid only for query parameters and allows sending a parameter with an empty value.
     *
     * Default value is false.
     *
     * If style is used, and if behavior is n/a (cannot be serialized), the value of allowEmptyValue SHALL be ignored.
     *
     * @var bool
     */
    public $allowEmptyValue = Undefined::UNDEFINED;

    /**
     * @inheritdoc
     */
    public static $_required = ['header', 'schema'];

    /**
     * @inheritdoc
     */
    public static $_types = [
        'header' => 'string',
        'description' => 'string',
    ];

    /**
     * @inheritdoc
     */
    public static $_nested = [
        Schema::class => 'schema',
        Attachable::class => ['attachables'],
    ];

    /**
     * @inheritdoc
     */
    public static $_parents = [
        Encoding::class,
        Components::class,
        Response::class,
    ];
}
