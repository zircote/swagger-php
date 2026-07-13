<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Augmenter;

use OpenApi\Spec as OA;

#[OA\Schema]
enum BackedStringEnum: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
}
