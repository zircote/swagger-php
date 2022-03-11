<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Generator;

/**
 * The discriminator is a specific object in a schema which is used to inform the consumer of
 * the specification of an alternative schema based on the value associated with it.
 *
 * This object is based on the [JSON Schema Specification](http://json-schema.org) and uses a predefined subset of it.
 * On top of this subset, there are extensions provided by this specification to allow for more complete documentation.
 *
 * @see [OAI Discriminator Object](https://github.com/OAI/OpenAPI-Specification/blob/main/versions/3.1.0.md#discriminatorObject)
 * @see [JSON Schema](http://json-schema.org/)
 *
 * @Annotation
 */
class Discriminator extends AbstractAnnotation
{
    /**
     * The name of the property in the payload that will hold the discriminator value.
     *
     * @var string
     */
    public $propertyName = Generator::UNDEFINED;

    /**
     * An object to hold mappings between payload values and schema names or references.
     *
     * @var string[]
     */
    public $mapping = Generator::UNDEFINED;

    /**
     * @inheritdoc
     */
    public static $_required = ['propertyName'];

    /**
     * @inheritdoc
     */
    public static $_types = [
        'propertyName' => 'string',
    ];

    /**
     * @inheritdoc
     */
    public static $_parents = [
        Schema::class,
        Property::class,
        AdditionalProperties::class,
        Items::class,
        JsonContent::class,
        XmlContent::class,
    ];

    /**
     * @inheritdoc
     */
    public static $_nested = [
        Attachable::class => ['attachables'],
    ];
}
