<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\PHP;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema
 */
class References
{
    /**
     * @OA\Property
     */
    public function &return_ref()
    {
        $var = 1;

        return $var;
    }
}
