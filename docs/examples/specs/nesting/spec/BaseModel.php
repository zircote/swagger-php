<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Nesting\Spec;

use OpenApi\Spec as OA;

#[OA\Schema]
class BaseModel
{
    #[OA\Property]
    public string $base;
}
