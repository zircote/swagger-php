<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Annotations;

use OpenApi\Annotations as OA;

/**
 * A <code>@OA\Request</code> query parameter.
 *
 * @Annotation
 *
 * @deprecated since 7.0, removed in 8.0 - use `OpenApi\Spec\Parameter\Query` instead
 */
class QueryParameter extends Parameter
{
    /**
     * @var string
     *             This takes 'query' as the default location
     */
    public $in = 'query';
}
