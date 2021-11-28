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
            string $ref = Generator::UNDEFINED,
            ?array $allOf = null,
            ?array $anyOf = null,
            ?array $oneOf = null,
            string $type = Generator::UNDEFINED,
            ?Items $items = null,
            ?array $x = null,
            ?array $attachables = null
        ) {
            parent::__construct($properties + [
                    'ref' => $ref,
                    'allOf' => $allOf ?? Generator::UNDEFINED,
                    'anyOf' => $anyOf ?? Generator::UNDEFINED,
                    'oneOf' => $oneOf ?? Generator::UNDEFINED,
                    'type' => $type,
                    'x' => $x ?? Generator::UNDEFINED,
                    'value' => $this->combine($items, $attachables),
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
