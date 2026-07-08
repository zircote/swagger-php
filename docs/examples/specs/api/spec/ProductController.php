<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Api\Spec;

use OpenApi\Spec as OA;

/**
 * The Controller.
 */
class ProductController
{
    /**
     * Get a product.
     *
     * @param ?int $product_id the product id
     */
    #[OA\Operation(path: '/products/{product_id}', method: 'get', operationId: 'getProducts', tags: ['products'])]
    #[OA\Response(
        response: 200,
        description: 'successful operation',
        headers: [new OA\Header(header: 'X-Rate-Limit', description: 'calls per hour allowed by the user', schema: new OA\Schema(type: 'integer', format: 'int32'))],
        content: [new OA\MediaType(mediaType: 'application/json', schema: new OA\Schema(ref: Product::class))],
    )]
    #[OA\Response(response: 401, description: 'oops')]
    public function getProduct(
        #[OA\Parameter(name: 'product_id', in: 'path', required: true)]
        ?int $product_id
    ) {
    }

    /**
     * Add a product.
     */
    #[OA\Operation(path: '/products', method: 'post', operationId: 'addProducts', summary: 'Add products', tags: ['products'])]
    #[OA\Response(
        response: 200,
        description: 'successful operation',
        content: [new OA\MediaType(mediaType: 'application/json', schema: new OA\Schema(ref: Product::class))],
    )]
    #[OA\RequestBody(
        description: 'New product',
        required: true,
        content: [new OA\MediaType(
            mediaType: 'application/json',
            schema: new OA\Schema(type: 'array', items: new OA\Schema(ref: Product::class)),
        )],
    )]
    public function addProduct()
    {
    }

    /**
     * Get all.
     */
    #[OA\Operation(path: '/products', method: 'get', operationId: 'getAll', tags: ['products', 'catalog'])]
    #[OA\Response(
        response: 200,
        description: 'successful operation',
        content: [new OA\MediaType(
            mediaType: 'application/json',
            schema: new OA\Schema(
                type: 'object',
                required: ['data'],
                properties: [
                    new OA\Property(
                        property: 'data',
                        schema: new OA\Schema(type: 'array', items: new OA\Schema(ref: Product::class)),
                    ),
                ],
            ),
        )],
    )]
    #[OA\Response(response: 401, description: 'oops')]
    public function getAll()
    {
    }

    #[OA\Operation(
        path: '/subscribe',
        method: 'post',
        operationId: 'subscribe',
        summary: 'Subscribe to product webhook',
        tags: ['products'],
        callbacks: [
            'onChange' => [
                '{$request.query.callbackUrl}' => [
                    'post' => [
                        'requestBody' => new OA\RequestBody(
                            description: 'subscription payload',
                            content: [
                                new OA\MediaType(
                                    mediaType: 'application/json',
                                    schema: new OA\Schema(
                                        properties: [
                                            new OA\Property(
                                                property: 'timestamp',
                                                schema: new OA\Schema(description: 'time of change', type: 'string', format: 'date-time'),
                                            ),
                                        ],
                                    ),
                                ),
                            ],
                        ),
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Your server implementation should return this HTTP status code if the data was received successfully',
                        ],
                    ],
                ],
            ],
        ],
    )]
    #[OA\Parameter(name: 'callbackUrl', in: 'query')]
    #[OA\Response(response: 200, description: 'callbackUrl registered')]
    public function subscribe()
    {
    }
}
