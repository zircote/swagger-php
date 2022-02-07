<?php declare(strict_types=1);

namespace OpenApi\Examples\UsingRefs;

/**
 * @OA\Schema
 */
enum StockLevel: int
{
    case AVAILABLE = 1;
    case SOLD_OUT = 2;
    case BACK_ORDER = 3;
}
