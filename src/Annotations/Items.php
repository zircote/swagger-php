<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

/**
 * @Annotation
 * The description of an item in a Schema with type "array"
 */
class Items extends Schema
{
    /**
     * {@inheritdoc}
     */
    public static $_nested = [
        Discriminator::class => 'discriminator',
        Items::class => 'items',
        Property::class => ['properties', 'property'],
        ExternalDocumentation::class => 'externalDocs',
        Xml::class => 'xml',
        AdditionalProperties::class => 'additionalProperties',
    ];

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function validate(array $parents = [], array $skip = [], string $ref = ''): bool
    {
        if (in_array($this, $skip, true)) {
            return true;
        }

        $valid = parent::validate($parents, $skip);

        $parent = end($parents);
        if ($parent instanceof Schema && $parent->type !== 'array') {
            $this->logger->notice(('@OA\\Items() parent type must be "array" in '.$this->_context);
            $valid = false;
        }

        if ($this->ref === UNDEFINED) {
            $parent = end($parents);
            if (is_object($parent) && ($parent instanceof Parameter && $parent->in !== 'body' || $parent instanceof Header)) {
                // This is a "Items Object" https://github.com/OAI/OpenAPI-Specification/blob/OpenAPI.next/versions/3.0.md#items-object
                // A limited subset of JSON-Schema's items object.
                $allowedTypes = ['string', 'number', 'integer', 'boolean', 'array'];
                if (in_array($this->type, $allowedTypes) === false) {
                    $this->logger->notice('@OA\Items()->type="'.$this->type.'" not allowed inside a '.$parent->_identity([]).' must be "'.implode('", "', $allowedTypes).'" in '.$this->_context);
                    $valid = false;
                }
            }
        }

        return $valid;
        // @todo Additional validation when used inside a Header or Parameter context.
    }
}
