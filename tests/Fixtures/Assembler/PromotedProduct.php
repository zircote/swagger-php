<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Assembler;

use OpenApi\Spec as OA;

#[OA\Schema(schema: 'PromotedProduct')]
class PromotedProduct
{
    public function __construct(
        #[OA\Property(property: 'quantity')]
        public int $quantity,
        #[OA\Property(property: 'brand')]
        #[OA\Schema(example: 'Acme')]
        public ?string $brand,
    ) {
    }
}
