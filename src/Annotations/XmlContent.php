<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Generator;

/**
 * Shorthand for a xml response.
 *
 * Use as an Schema inside a Response and the MediaType "application/xml" will be generated.
 *
 * @Annotation
 */
abstract class AbstractXmlContent extends Schema
{
    /**
     * @var Examples
     */
    public $examples = Generator::UNDEFINED;

    /**
     * @inheritdoc
     */
    public static $_parents = [];

    /**
     * @inheritdoc
     */
    public static $_nested = [
        Discriminator::class => 'discriminator',
        Items::class => 'items',
        Property::class => ['properties', 'property'],
        ExternalDocumentation::class => 'externalDocs',
        Xml::class => 'xml',
        AdditionalProperties::class => 'additionalProperties',
        Examples::class => ['examples', 'example'],
        Attachable::class => ['attachables'],
    ];
}

if (\PHP_VERSION_ID >= 80100) {
    /**
     * @Annotation
     */
    #[\Attribute(\Attribute::TARGET_CLASS)]
    class XmlContent extends AbstractXmlContent
    {
        public function __construct(
            array $properties = [],
            $examples = Generator::UNDEFINED,
            $x = Generator::UNDEFINED
        ) {
            parent::__construct($properties + [
                    'x' => $x,
                    'value' => $this->combine($examples),
                ]);
        }
    }
} else {
    /**
     * @Annotation
     */
    class XmlContent extends AbstractXmlContent
    {
        public function __construct(array $properties)
        {
            parent::__construct($properties);
        }
    }
}
