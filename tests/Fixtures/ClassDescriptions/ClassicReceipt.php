<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\ClassDescriptions;

use OpenApi\Attributes as OAT;

#[OAT\Schema(schema: 'ClassicReceiptTotal', type: 'integer')]
#[OAT\Schema(schema: 'ClassicReceiptLine', type: 'string')]
#[OAT\Schema(type: 'object')]
class ClassicReceipt
{
}
