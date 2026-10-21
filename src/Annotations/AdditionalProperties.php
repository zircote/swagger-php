<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

/**
 * @Annotation
 *
 * @deprecated since 7.0, removed in 8.0 - use `OpenApi\Spec\Schema\AdditionalProperties` instead
 */
class AdditionalProperties extends Schema
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
}
