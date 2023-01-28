<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\PHP\Enums;

use OpenApi\Attributes as OAT;

#[OAT\Schema(type: 'string')]
enum StatusEnumStringBacked: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case ARCHIVED = 'archived';
}
