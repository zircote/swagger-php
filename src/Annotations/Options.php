<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

/**
 * @Annotation
 *
 * @deprecated since 7.0, removed in 8.0 - use `OpenApi\Spec\Operation\Options` instead
 */
class Options extends Operation
{
    /**
     * @var string
     */
    public $method = 'options';

    /**
     * @inheritdoc
     */
    public static $_parents = [
        PathItem::class,
    ];
}
