<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

/**
 * @Annotation
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Delete extends Operation
{
    /**
     * @inheritdoc
     */
    public $method = 'delete';

    /**
     * @inheritdoc
     */
    public static $_parents = [
        PathItem::class,
    ];
}
