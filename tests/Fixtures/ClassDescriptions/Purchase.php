<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\ClassDescriptions;

use OpenApi\Spec as OA;

#[OA\Schema]
class Purchase
{
    #[OA\Property]
    public Receipt $receipt;

    #[OA\Property(schema: new OA\Schema\Ref(ref: Receipt::class))]
    public mixed $copy;
}
