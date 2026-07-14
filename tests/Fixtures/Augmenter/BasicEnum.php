<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

use OpenApi\Spec as OA;

#[OA\Schema]
enum BasicEnum
{
    case GREEN;
    case BLUE;
    case RED;
}
