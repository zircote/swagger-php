<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

/**
 * @Annotation
 */
class Trace extends Operation
{
    /**
     * @var string
     */
    public $method = 'trace';

    /**
     * @inheritdoc
     */
    public static $_parents = [
        PathItem::class,
    ];
}
