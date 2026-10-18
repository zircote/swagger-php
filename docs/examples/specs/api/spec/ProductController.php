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
    #[OA\Operation\Get(path: '/products/{product_id}', operationId: 'getProducts', tags: ['products'])]
    #[OA\Response(
        response: 200,
        description: 'successful operation',
        headers: [new OA\Header(header: 'X-Rate-Limit', description: 'calls per hour allowed by the user', schema: new OA\Schema(type: OA\Type::Integer, format: OA\Format::Int32))],
        content: [new OA\MediaType\Json(ref: Product::class)],
    )]
    #[OA\Response(response: 401, description: 'oops')]
    public function getProduct(
        #[OA\Parameter\Path(name: 'product_id')]
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
        content: [new OA\MediaType\Json(ref: Product::class)],
    )]
    #[OA\RequestBody(
        description: 'New product',
        required: true,
        content: [new OA\MediaType\Json(items: new OA\Schema(ref: Product::class))],
    )]
    public function addProduct()
    {
    }

    /**
     * Get all.
     */
    #[OA\Operation(path: '/products', method: OA\HttpMethod::Get, operationId: 'getAll', tags: ['products'])]
    #[OA\Tag(name: 'catalog')]
    #[OA\Response(
        response: 200,
        description: 'successful operation',
        content: [new OA\MediaType(
            mediaType: 'application/json',
            schema: new OA\Schema(
                type: 'object',
                properties: [
                    new OA\Property(
                        property: 'data',
                        schema: new OA\Schema(type: 'array', items: new OA\Schema(ref: Product::class)),
                    ),
                ],
                required: ['data'],
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
                                                schema: new OA\Schema(description: 'time of change', type: 'string', format: OA\Format::DateTime),
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
    #[OA\Parameter(name: 'callbackUrl', in: OA\ParameterIn::Query)]
    #[OA\Response(response: 200, description: 'callbackUrl registered')]
    public function subscribe()
    {
    }
}
