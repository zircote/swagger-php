<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

use OpenApi\Spec as OA;

#[OA\Schema(type: 'integer')]
enum BackedIntEnum: int
{
    case GREEN = 1;
    case BLUE = 2;
    case RED = 3;
}
