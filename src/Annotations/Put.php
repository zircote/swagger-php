<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

/**
 * @Annotation
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class Put extends Operation
{
    /**
     * @inheritdoc
     */
    public $method = 'put';

    /**
     * @inheritdoc
     */
    public static $_parents = [
        PathItem::class,
    ];
}
