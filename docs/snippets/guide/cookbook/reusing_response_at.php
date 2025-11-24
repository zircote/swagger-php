<?php

namespace Openapi\Snippets\Cookbook\ReusingResponse;

use OpenApi\Attributes as OA;

#[OA\Response(
    response: 'product',
    description: 'All information about a product',
    content: new OA\JsonContent(ref: '#/components/schemas/Product'),
)]
class ProductResponse
{
}

// ...

class ProductController
{
    #[OA\Get(
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
