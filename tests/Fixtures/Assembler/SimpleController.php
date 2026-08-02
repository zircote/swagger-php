<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Assembler;

use OpenApi\Spec as OA;
use OpenApi\Tests\Fixtures\Assembler\Attachable\RequestPayload;

class SimpleController
{
    #[OA\Operation\Post(path: '/products/{product_id}')]
    #[OA\Response(response: 200, description: 'OK')]
    public function addProduct(
        #[OA\Parameter(name: 'product_id', in: 'path', required: true)]
        #[OA\Schema(format: 'int64')]
        ?int $product_id,
        #[RequestPayload]
        SimpleProduct $product,
    ) {
    }
}
