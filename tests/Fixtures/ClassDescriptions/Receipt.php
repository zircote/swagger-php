<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\ClassDescriptions;

use OpenApi\Spec as OA;

#[OA\Schema(component: 'ReceiptTotal', type: 'integer')]
#[OA\Schema(component: 'ReceiptLine', type: 'string')]
#[OA\Schema(type: 'object')]
class Receipt
{
}
