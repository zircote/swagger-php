<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Generator;

/**
 * @see [XML Object](https://spec.openapis.org/oas/v3.1.1.html#xml-object)
 *
 * @Annotation
 */
class Xml extends AbstractAnnotation
{
    /**
     * Replaces the name of the element/attribute used for the described schema property.
     *
     * When defined within the Items Object (items), it will affect the name of the individual XML elements within the list.
     * When defined alongside type being array (outside the items), it will affect the wrapping element
     * and only if wrapped is <code>true</code>.
     *
     * If wrapped is <code>false</code>, it will be ignored.
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
     * Declares whether the property definition translates to an attribute instead of an element.
     *
     * Default value is <code>false</code>.
     *
     * @var bool
     */
    public $attribute = Generator::UNDEFINED;

    /**
     * MAY be used only for an array definition.
     *
     * Signifies whether the array is wrapped (for example  <code>&lt;books>&lt;book/>&lt;book/>&lt;/books></code>)
     * or unwrapped (<code>&lt;book/>&lt;book/></code>).
     *
     * Default value is false. The definition takes effect only when defined alongside type being array (outside the items).
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
