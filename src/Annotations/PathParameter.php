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
}
