<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Generator;

/**
 * The description of an item in a Schema with type "array".
 *
 * @Annotation
 */
abstract class AbstractItems extends Schema
{
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

    /**
     * @inheritdoc
     */
    public static $_parents = [
        Property::class,
        AdditionalProperties::class,
        Schema::class,
        JsonContent::class,
        XmlContent::class,
        Items::class,
    ];

    /**
     * @inheritdoc
     */
    public function validate(array $parents = [], array $skip = [], string $ref = ''): bool
    {
        if (in_array($this, $skip, true)) {
            return true;
        }

        $valid = parent::validate($parents, $skip);

        $parent = end($parents);
        if ($parent instanceof Schema && $parent->type !== 'array') {
            $this->_context->logger->warning('@OA\\Items() parent type must be "array" in ' . $this->_context);
            $valid = false;
        }

        return $valid;
        // @todo Additional validation when used inside a Header or Parameter context.
    }
}

if (\PHP_VERSION_ID >= 80100) {
    /**
     * @Annotation
     */
    #[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
    class Items extends AbstractItems
    {
        public function __construct(
            array $properties = [],
            string $type = Generator::UNDEFINED,
            string $ref = Generator::UNDEFINED,
            ?bool $deprecated = null,
            ?array $allOf = null,
            ?array $anyOf = null,
            ?array $oneOf = null,
            ?bool $nullable = null,
            ?array $x = null,
            ?array $attachables = null
        ) {
            parent::__construct($properties + [
                    'type' => $type,
                    'ref' => $ref,
                    'nullable' => $nullable ?? Generator::UNDEFINED,
                    'deprecated' => $deprecated ?? Generator::UNDEFINED,
                    'allOf' => $allOf ?? Generator::UNDEFINED,
                    'anyOf' => $anyOf ?? Generator::UNDEFINED,
                    'oneOf' => $oneOf ?? Generator::UNDEFINED,
                    'x' => $x ?? Generator::UNDEFINED,
                    'value' => $this->combine($attachables),
                ]);
        }
    }
} else {
    /**
     * @Annotation
     */
    class Items extends AbstractItems
    {
        public function __construct(array $properties)
        {
            parent::__construct($properties);
        }
    }
}
