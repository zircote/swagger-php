<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

/**
 * @Annotation
 *
 * @deprecated since 7.0, removed in 8.0 - use `OpenApi\Spec\Operation\Post` instead
 */
class Post extends Operation
{
    /**
     * @var string
     */
    public $method = 'post';

    /**
     * @inheritdoc
     */
    public static $_parents = [
        PathItem::class,
    ];
}
