<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\PHP\Enums;

use OpenApi\Attributes as OAT;

#[OAT\Schema]
enum StatusEnum
{
    case DRAFT;
    case PUBLISHED;
    case ARCHIVED;
}
