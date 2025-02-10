<?php declare(strict_types=1);

namespace OpenApi\Examples\Specs\UsingLinks\Attributes;

use OpenApi\Attributes\Schema;

#[Schema()]
enum State
{
    case OPEN;
    case MERGED;
    case DECLINED;
}
