<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

/**
 * A <code>@OA\Request</code> cookie parameter.
 *
 * @Annotation
 */
class CookieParameter extends Parameter
{
    /**
     * @var string
     */
    public $in = 'cookie';
}
