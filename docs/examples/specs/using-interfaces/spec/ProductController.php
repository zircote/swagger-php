<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\UsingInterfaces\Spec;

use OpenApi\Spec as OA;

class ProductController
{
    #[OA\Operation\Get(
        path: '/products/{id}',
        operationId: 'getProduct',
        description: 'Get product in any colour for id',
        tags: ['api'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'ID of product to return',
                required: true,
                schema: new OA\Schema(type: 'string'),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'successful operation',
                content: [new OA\MediaType(
                    mediaType: 'application/json',
                    schema: new OA\Schema(ref: '#/components/schemas/Product'),
                )],
            ),
        ],
    )]
    public function getProduct($id)
    {
    }

    #[OA\Operation\Get(
        path: '/products/green/{id}',
        operationId: 'getGreenProduct',
        description: 'Get green products',
        tags: ['api'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                description: 'ID of product to return',
                required: true,
                schema: new OA\Schema(type: 'string'),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'successful operation',
                content: [new OA\MediaType(
                    mediaType: 'application/json',
                    schema: new OA\Schema(ref: '#/components/schemas/GreenProduct'),
                )],
            ),
        ],
    )]
    public function getGreenProduct($id)
    {
    }
}
