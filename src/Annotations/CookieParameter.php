<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

/**
 * A <code>@OA\Request</code> cookie parameter.
 *
 * @Annotation
 *
 * @deprecated since 7.0, removed in 8.0 - use `OpenApi\Spec\Parameter\Cookie` instead
 */
class CookieParameter extends Parameter
{
    /**
     * @var string
     */
    public $in = 'cookie';
}
