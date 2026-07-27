<?php

namespace Openapi\Snippets\Cookbook\ReusingResponse;

use OpenApi\Spec as OA;

#[OA\Response(
    response: 'product',
    description: 'All information about a product',
    content: [new OA\MediaType(
        mediaType: 'application/json',
        schema: new OA\Schema(ref: '#/components/schemas/Product'),
    )],
)]
class ProductResponse
{
}

// ...

class ProductController
{
    #[OA\Operation\Get(
        tags: ['Products'],
        path: '/products/{product_id}',
        responses: [
            new OA\Response(
                response: 'default',
                ref: '#/components/responses/product'
            ),
        ],
    )]
    public function getProduct($id)
    {
    }
}
