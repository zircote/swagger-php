<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

/**
 * A request path parameter.
 *
 * @Annotation
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_PARAMETER | \Attribute::IS_REPEATABLE)]
class PathParameter extends Parameter
{
    /**
     * @inheritdoc
     */
    public $in = 'path';

    /**
     * @inheritdoc
     */
    public $required = true;

    /**
     * @inheritdoc
     */
    public static $_parents = [
        Components::class,
        PathItem::class,
        Operation::class,
        Get::class,
        Post::class,
        Put::class,
        Delete::class,
        Patch::class,
        Head::class,
        Options::class,
        Trace::class,
    ];
}
