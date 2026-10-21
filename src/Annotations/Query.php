<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

/**
 * @since OpenAPI 3.2.0
 *
 * @Annotation
 *
 * @deprecated since 7.0, removed in 8.0 - use `OpenApi\Spec\Operation` with `method: 'query'` instead
 */
class Query extends Operation
{
    /**
     * @var string
     */
    public $method = 'query';

    /**
     * @inheritdoc
     */
    public static $_parents = [
        PathItem::class,
    ];
}
