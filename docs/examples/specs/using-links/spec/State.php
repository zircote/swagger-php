<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\UsingLinks\Spec;

use OpenApi\Spec as OA;

#[OA\Schema]
enum State
{
    case OPEN;
    case MERGED;
    case DECLINED;
}
