<?php declare(strict_types=1);
/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Generator;

/**
 * A "Header Object" https://github.com/OAI/OpenAPI-Specification/blob/OpenAPI.next/versions/3.0.md#headerObject.
 *
 * @Annotation
 */
abstract class AbstractHeader extends AbstractAnnotation
{
    /**
     * $ref See https://swagger.io/docs/specification/using-ref/.
     *
     * @var string
     */
    public $ref = Generator::UNDEFINED;

    /**
     * @var string
     */
    public $header = Generator::UNDEFINED;

    /**
     * @var string
     */
    public $description = Generator::UNDEFINED;

    /**
     * A brief description of the parameter. This could contain examples of use. CommonMark syntax MAY be used for rich text representation.
     *
     * @var bool
     */
    public $required = Generator::UNDEFINED;

    /**
     * Schema object.
     *
     * @var \OpenApi\Annotations\Schema
     */
    public $schema = Generator::UNDEFINED;

    /**
     * Specifies that a parameter is deprecated and SHOULD be transitioned out of usage.
     *
     * @var bool
     */
    public $deprecated = Generator::UNDEFINED;

    /**
     * Sets the ability to pass empty-valued parameters.
     * This is valid only for query parameters and allows sending a parameter with an empty value.
     * Default value is false. If style is used, and if behavior is n/a
     * (cannot be serialized), the value of allowEmptyValue SHALL be ignored.
     *
     * @var bool
     */
    public $allowEmptyValue = Generator::UNDEFINED;

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
        Components::class,
        Response::class,
    ];
}

if (\PHP_VERSION_ID >= 80100) {
    /**
     * @Annotation
     */
    #[\Attribute(\Attribute::TARGET_CLASS)]
    class Header extends AbstractHeader
    {
        public function __construct(
            array $properties = [],
            ?array $x = null,
            ?array $attachables = null
        ) {
            parent::__construct($properties + [
                    'x' => $x ?? Generator::UNDEFINED,
                    'value' => $this->combine($attachables),
                ]);
        }
    }
} else {
    /**
     * @Annotation
     */
    class Header extends AbstractHeader
    {
        public function __construct(array $properties)
        {
            parent::__construct($properties);
        }
    }
}
