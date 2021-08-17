<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Generator;

/**
 * Shorthand for a json response.
 *
 * Use as an Schema inside a Response and the MediaType "application/json" will be generated.
 *
 * @Annotation
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
abstract class AbstractJsonContent extends Schema
{

    /**
     * @var object
     */
    public $example = Generator::UNDEFINED;

    /**
     * @var object
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
    class JsonContent extends AbstractJsonContent
    {
        public function __construct(
            array $properties = [],
            string $ref = Generator::UNDEFINED,
            string $type = Generator::UNDEFINED,
            $items = Generator::UNDEFINED
        ) {
            parent::__construct($properties + [
                    'ref' => $ref,
                    'type' => $type,
                    'value' => $this->combine($items),
                ]);
        }
    }
} else {
    /**
     * @Annotation
     */
    class JsonContent extends AbstractJsonContent
    {
        public function __construct(array $properties)
        {
            parent::__construct($properties);
        }
    }
}
