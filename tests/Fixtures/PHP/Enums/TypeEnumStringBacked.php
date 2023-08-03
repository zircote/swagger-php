<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\PHP\Enums;

use OpenApi\Attributes as OAT;

#[OAT\Schema(type: 'string')]
enum TypeEnumStringBacked: string
{
    case FEATURE = 'feature';
    case BUG = 'bug';
    case IMPROVEMENT = 'improvement';
}
