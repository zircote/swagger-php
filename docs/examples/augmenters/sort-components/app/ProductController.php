<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace App;

use OpenApi\Spec as OA;

class ProductController
{
    #[OA\Operation\Get(path: '/products')]
    #[OA\Response(response: 200, description: 'The products', content: [new OA\MediaType\Json(items: new OA\Schema(ref: Product::class))])]
    public function products()
    {
    }

    #[OA\Operation\Get(path: '/categories')]
    #[OA\Response(response: 200, description: 'The categories', content: [new OA\MediaType\Json(items: new OA\Schema(ref: Category::class))])]
    public function categories()
    {
    }

    #[OA\Operation\Get(path: '/brands')]
    #[OA\Response(response: 200, description: 'The brands', content: [new OA\MediaType\Json(items: new OA\Schema(ref: Brand::class))])]
    public function brands()
    {
    }
}
