<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace App;

use OpenApi\Spec as OA;

#[OA\Schema]
class Product
{
    public function __construct(
        #[OA\Property]
        public int $id,
    ) {
    }
}
