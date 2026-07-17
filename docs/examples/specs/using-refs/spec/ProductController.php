<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\UsingRefs\Spec;

use OpenApi\Spec as OA;

#[OA\PathItem(parameters: [
    new OA\Parameter(ref: '#/components/parameters/product_id_in_path_required'),
])]
class ProductController
{
    #[OA\Operation\Get(
        path: '/products/{product_id}',
        operationId: 'getProduct',
        tags: ['Products'],
        responses: [
            new OA\Response(response: 'default', ref: '#/components/responses/product'),
        ],
    )]
    public function getProduct($id)
    {
    }

    #[OA\Operation\Patch(
        path: '/products/{product_id}',
        operationId: 'updateProduct',
        tags: ['Products'],
        requestBody: new OA\RequestBody(ref: '#/components/requestBodies/product_in_body'),
        responses: [
            new OA\Response(response: 'default', ref: '#/components/responses/product'),
        ],
    )]
    public function updateProduct($id)
    {
    }
}
