<?php declare(strict_types=1);

namespace OpenApi\Tests\Fixtures\PHP;

use OpenApi\Attributes\Schema;

#[Schema()]
enum StatusEnumStringBacked: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case ARCHIVED = 'archived';
}
