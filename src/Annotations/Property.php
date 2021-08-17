<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Generator;

/**
 * @Annotation
 */
#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY)]
abstract class AbstractProperty extends Schema
{
    /**
     * The key into Schema->properties array.
     *
     * @var string
     */
    public $property = Generator::UNDEFINED;

    /**
     * Indicates the property is nullable.
     *
     * @var bool
     */
    public $nullable = Generator::UNDEFINED;

    /**
     * @inheritdoc
     */
    public static $_parents = [
        AdditionalProperties::class,
        Schema::class,
        JsonContent::class,
        XmlContent::class,
        Property::class,
        Items::class,
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
    #[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY)]
    class Property extends AbstractProperty
    {
        public function __construct(
            array $properties = [],
            string $description = Generator::UNDEFINED,
            string $title = Generator::UNDEFINED,
            string $type = Generator::UNDEFINED,
            string $format = Generator::UNDEFINED,
            string $ref = Generator::UNDEFINED,
            $example = Generator::UNDEFINED,
            $x = Generator::UNDEFINED
        ) {
            parent::__construct($properties + [
                    'description' => $description,
                    'title' => $title,
                    'type' => $type,
                    'format' => $format,
                    'ref' => $ref,
                    'example' => $example,
                    'x' => $x,
                ]);
        }
    }
} else {
    /**
     * @Annotation
     */
    class Property extends AbstractProperty
    {
        public function __construct(array $properties)
        {
            parent::__construct($properties);
        }
    }
}
