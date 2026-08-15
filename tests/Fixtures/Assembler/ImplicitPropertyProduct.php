<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Assembler;

use OpenApi\Spec as OA;

#[OA\Schema(schema: 'ImplicitPropertyProduct')]
class ImplicitPropertyProduct
{
    #[OA\Schema(format: 'int64')]
    public int $id;

    public function __construct(
        #[OA\Schema(description: 'The colour')]
        public string $colour,
    ) {
    }
}
