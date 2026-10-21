<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

/**
 * The description of an item in a Schema with type <code>array</code>.
 *
 * @Annotation
 *
 * @deprecated since 7.0, removed in 8.0 - use `OpenApi\Spec\Schema\Items` instead
 */
class Items extends Schema
{
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
}
