<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

/**
 * A `@OA\Request` cookie parameter.
 *
 * @Annotation
 */
class CookieParameter extends Parameter
{
    /**
     * @inheritdoc
     */
    public $in = 'cookie';
}
