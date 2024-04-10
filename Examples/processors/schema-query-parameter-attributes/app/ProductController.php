<?php

declare(strict_types=1);

namespace App;

use OpenApi\Attributes as OA;
use OpenApi\Attributes\Response;
use SchemaQueryParameterProcessor\SchemaQueryParameter;

class ProductController
{
    #[OA\Get(
        path: '/products/{id}',
        tags: ['Products'],
        parameters: [
            new OA\PathParameter(
                name: 'id',
                required: true,
            ),
        ],
        responses: [
            new Response(
                response: 200,
                description: 'A single product',
                content: new OA\JsonContent(
                    ref: Product::class
                )
            ),
        ],
    )]
    public function getProduct($id) {}

    #[OA\Get(
        path: '/products/search',
        tags: ['Products'],
        responses: [
            new Response(
                response: 200,
                description: 'A single product',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: Product::class)
                )
            ),
        ],
        x: [SchemaQueryParameter::REF => Product::class],
    )]
    public function findProducts($id) {}
}
