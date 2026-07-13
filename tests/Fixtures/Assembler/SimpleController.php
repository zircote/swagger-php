<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Assembler;

use OpenApi\Spec as OA;

class SimpleController
{
    #[OA\Operation(path: '/products/{product_id}', method: 'get')]
    #[OA\Response(response: 200, description: 'OK')]
    public function getProduct(
        #[OA\Parameter(name: 'product_id', in: 'path', required: true)]
        #[OA\Schema(format: 'int64')]
        ?int $product_id,
    ) {
    }
}
