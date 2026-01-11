<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\PHP;

use OpenApi\Attributes as OAT;

#[OAT\Schema]
class References
{
    #[OAT\Property]
    public function &return_ref()
    {
        $var = 1;

        return $var;
    }
}
