<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Generator;

/**
 * @Annotation
 */
class Examples extends AbstractAnnotation
{
    /**
     * The relative or absolute path to an example.
     *
     * @see [Reference Object](https://spec.openapis.org/oas/v3.1.1.html#reference-object)
     *
     * @var string|class-string|object
     */
    public $ref = Generator::UNDEFINED;

    /**
     * The key into <code>#/components/examples</code>.
     *
     * @var string
     */
    public $example = Generator::UNDEFINED;

    /**
     * Short description for the example.
     *
     * @var string
     */
    public $summary = Generator::UNDEFINED;

    /**
     * Embedded literal example.
     *
     * The value field and externalValue field are mutually exclusive.
     *
     * To represent examples of media types that cannot naturally be represented
     * in JSON or YAML, use a string value to contain the example, escaping where necessary.
     *
     * @var string
     */
    public $description = Generator::UNDEFINED;

    /**
     * Embedded literal example.
     *
     * The value field and externalValue field are mutually exclusive.
     *
     * To represent examples of media types that cannot naturally be represented
     * in JSON or YAML, use a string value to contain the example, escaping where necessary.
     *
     * @var int|string|array
     */
    public $value = Generator::UNDEFINED;

    /**
     * An URL that points to the literal example.
     *
     * This provides the capability to reference examples that cannot easily be included
     * in JSON or YAML documents.
     *
     * The value field and externalValue field are mutually exclusive.
     *
     * @var string
     */
    public $externalValue = Generator::UNDEFINED;

    public static $_types = [
        'summary' => 'string',
        'description' => 'string',
        'externalValue' => 'string',
    ];

    public static $_required = ['summary'];

    public static $_parents = [
        Components::class,
        Schema::class,
        Parameter::class,
        PathParameter::class,
        MediaType::class,
        JsonContent::class,
        XmlContent::class,
    ];

    /**
     * @inheritdoc
     */
    public static $_nested = [
        Attachable::class => ['attachables'],
    ];
}
