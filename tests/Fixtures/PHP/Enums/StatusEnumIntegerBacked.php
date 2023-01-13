<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\PHP\Enums;

use OpenApi\Attributes as OAT;

#[OAT\Schema(type: 'integer')]
enum StatusEnumIntegerBacked: int
{
    case DRAFT = 1;
    case PUBLISHED = 2;
    case ARCHIVED = 3;
}
