<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

/**
 * The description of an item in a Schema with type <code>array</code>.
 *
 * @Annotation
 */
class Items extends Schema
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
    public function validate(array $stack = [], array $skip = [], string $ref = '', $context = null): bool
    {
        if (in_array($this, $skip, true)) {
            return true;
        }

        $valid = parent::validate($stack, $skip, $ref, $context);

        $parent = end($stack);
        // type might be array in 3.1.0
        if ($parent instanceof Schema && ($parent->type !== 'array' && !(is_array($parent->type) && in_array('array', $parent->type)))) {
            $this->_context->logger->warning('@OA\\Items() parent type must be "array" in ' . $this->_context);
            $valid = false;
        }

        return $valid;
    }
}
