<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Generator;

/**
 * A "XML Object": https://github.com/OAI/OpenAPI-Specification/blob/OpenAPI.next/versions/3.0.md#xmlObject.
 *
 * @Annotation
 */
abstract class AbstractXml extends AbstractAnnotation
{
    /**
     * Replaces the name of the element/attribute used for the described schema property. When defined within the Items Object (items), it will affect the name of the individual XML elements within the list. When defined alongside type being array (outside the items), it will affect the wrapping element and only if wrapped is true. If wrapped is false, it will be ignored.
     *
     * @var string
     */
    public $name = Generator::UNDEFINED;

    /**
     * The URL of the namespace definition. Value SHOULD be in the form of a URL.
     *
     * @var string
     */
    public $namespace = Generator::UNDEFINED;

    /**
     * The prefix to be used for the name.
     *
     * @var string
     */
    public $prefix = Generator::UNDEFINED;

    /**
     * Declares whether the property definition translates to an attribute instead of an element. Default value is false.
     *
     * @var bool
     */
    public $attribute = Generator::UNDEFINED;

    /**
     * MAY be used only for an array definition. Signifies whether the array is wrapped (for example, <books><book/><book/></books>) or unwrapped (<book/><book/>). Default value is false. The definition takes effect only when defined alongside type being array (outside the items).
     *
     * @var bool
     */
    public $wrapped = Generator::UNDEFINED;

    /**
     * @inheritdoc
     */
    public static $_types = [
        'name' => 'string',
        'namespace' => 'string',
        'prefix' => 'string',
        'attribute' => 'boolean',
        'wrapped' => 'boolean',
    ];

    /**
     * @inheritdoc
     */
    public static $_parents = [
        AdditionalProperties::class,
        Schema::class,
        Property::class,
        Schema::class,
        Items::class,
        XmlContent::class,
    ];

    /**
     * @inheritdoc
     */
    public static $_nested = [
        Attachable::class => ['attachables'],
    ];
}

if (\PHP_VERSION_ID >= 80100) {
    /**
     * @Annotation
     */
    #[\Attribute(\Attribute::TARGET_CLASS)]
    class Xml extends AbstractXml
    {
        public function __construct(
            array $properties = [],
            string $name = Generator::UNDEFINED,
            string $namespace = Generator::UNDEFINED,
            string $prefix = Generator::UNDEFINED,
            ?bool $attribute = null,
            ?bool $wrapped = null,
            ?array $x = null,
            ?array $attachables = null
        ) {
            parent::__construct($properties + [
                    'name' => $name,
                    'namespace' => $namespace,
                    'prefix' => $prefix,
                    'attribute' => $attribute ?? Generator::UNDEFINED,
                    'wrapped' => $wrapped ?? Generator::UNDEFINED,
                    'x' => $x ?? Generator::UNDEFINED,
                    'value' => $this->combine($attachables),
                ]);
        }
    }
} else {
    /**
     * @Annotation
     */
    class Xml extends AbstractXml
    {
        public function __construct(array $properties)
        {
            parent::__construct($properties);
        }
    }
}
