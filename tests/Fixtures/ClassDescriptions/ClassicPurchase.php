<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\ClassDescriptions;

use OpenApi\Attributes as OAT;

#[OAT\Schema]
class ClassicPurchase
{
    #[OAT\Property(ref: ClassicReceipt::class)]
    public $receipt;
}
