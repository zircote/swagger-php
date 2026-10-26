<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace App;

use OpenApi\Spec as OA;
use SchemaQueryParameterAugmenter\SchemaQueryParameter;

class ProductController
{
    #[OA\Operation\Get(path: '/products/{id}', tags: ['Products'])]
    #[OA\Response(
        response: 200,
        description: 'A single product',
        content: [new OA\MediaType\Json(ref: Product::class)],
    )]
    public function getProduct(
        #[OA\Parameter\Path(name: 'id')]
        int $id,
    ) {
    }

    #[OA\Operation\Get(
        path: '/products/search',
        tags: ['Products'],
        x: [SchemaQueryParameter::REF => Product::class],
    )]
    #[OA\Response(
        response: 200,
        description: 'A list of products',
        content: [new OA\MediaType\Json(items: new OA\Schema(ref: Product::class))],
    )]
    public function findProducts()
    {
    }
}
