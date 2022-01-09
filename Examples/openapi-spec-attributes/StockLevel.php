<?php declare(strict_types=1);

namespace OpenApi\Examples\OpenapiSpecAttributes;

use OpenApi\Attributes\Schema;

#[Schema()]
enum StockLevel
{
    case AVAILABLE;
    case SOLD_OUT;
    case BACK_ORDER;
}
