<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Generator;

/**
 * @Annotation
 */
abstract class AbstractAdditionalProperties extends Schema
{
    /**
     * @inheritdoc
     */
    public static $_parents = [
        Schema::class,
        Property::class,
        Items::class,
        JsonContent::class,
        XmlContent::class,
        AdditionalProperties::class,
    ];

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
        Attachable::class => ['attachables'],
    ];
}

if (\PHP_VERSION_ID >= 80100) {
    /**
     * @Annotation
     */
    #[\Attribute(\Attribute::TARGET_CLASS)]
    class AdditionalProperties extends AbstractAdditionalProperties
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
    class AdditionalProperties extends AbstractAdditionalProperties
    {
        public function __construct(array $properties)
        {
            parent::__construct($properties);
        }
    }
}
