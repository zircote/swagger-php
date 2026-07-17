<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\UsingTraits\Spec;

use OpenApi\Spec as OA;

class ProductController
{
    use DeleteEntity;

    #[OA\Operation\Get(
        path: '/products/{product_id}',
        operationId: 'getProduct',
        tags: ['Products'],
        parameters: [
            new OA\Parameter(
                name: 'product_id',
                in: 'path',
                description: 'ID of product to return',
                required: true,
                schema: new OA\Schema(type: 'string'),
            ),
        ],
        responses: [
            new OA\Response(
                response: 'default',
                description: 'successful operation',
                content: [new OA\MediaType(
                    mediaType: 'application/json',
                    schema: new OA\Schema(oneOf: [
                        new OA\Schema(ref: '#/components/schemas/SimpleProduct'),
                        new OA\Schema(ref: '#/components/schemas/Product'),
                        new OA\Schema(ref: '#/components/schemas/TrickyProduct'),
                    ]),
                )],
            ),
        ],
    )]
    public function getProduct($id)
    {
    }
}
