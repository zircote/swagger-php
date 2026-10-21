<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Annotations as OA;

/**
 * A <code>@OA\Request</code> header parameter.
 *
 * @Annotation
 *
 * @deprecated since 7.0, removed in 8.0 - use `OpenApi\Spec\Parameter\Header` instead
 */
class HeaderParameter extends Parameter
{
    /**
     * @var string
     */
    public $in = 'header';
}
