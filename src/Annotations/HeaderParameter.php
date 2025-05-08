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
 */
class HeaderParameter extends Parameter
{
    /**
     * @inheritdoc
     * This takes 'header' as the default location.
     */
    public $in = 'header';
}
