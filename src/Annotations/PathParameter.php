<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

/**
 * A `@OA\Request` path parameter.
 *
 * @Annotation
 */
class PathParameter extends Parameter
{
    /**
     * @inheritdoc
     * This takes 'path' as the default location.
     */
    public $in = 'path';

    /**
     * @inheritdoc
     */
    public $required = true;
}
