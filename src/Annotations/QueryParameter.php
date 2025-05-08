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
 */
class QueryParameter extends Parameter
{
    /**
     * @inheritdoc
     * This takes 'query' as the default location.
     */
    public $in = 'query';
}
