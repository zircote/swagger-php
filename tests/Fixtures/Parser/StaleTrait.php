<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Parser;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(schema="stale")
 */
trait StaleTrait
{
    /**
     * @OA\Property
     */
    public $expired = true;
}
