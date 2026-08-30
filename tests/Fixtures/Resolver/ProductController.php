<?php declare(strict_types=1);

/*
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Resolver;

use OpenApi\Spec as OA;

#[OA\Info(title: 'Resolver', version: '1.0.0')]
class ProductController
{
    #[OA\Operation\Get(path: '/products')]
    #[OA\Response(response: 200, description: 'OK', content: [
        new OA\MediaType(mediaType: 'application/json', schema: new OA\Schema(ref: Product::class)),
    ])]
    public function list()
    {
    }
}
