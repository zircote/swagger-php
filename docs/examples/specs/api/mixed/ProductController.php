<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Api\Mixed;

use OpenApi\Annotations as OA;
use OpenApi\Attributes as OAT;

/**
 * The Controller.
 */
class ProductController
{
    /**
     * Get a product.
     *
     * @param ?int $product_id ignored product id docblock typehint
     */
    #[OAT\Get(path: '/products/{product_id}', operationId: 'getProducts', tags: ['products'], responses: [
        new OAT\Response(
            response: 200,
            description: 'successful operation',
            headers: [
                new OAT\Header(header: 'X-Rate-Limit', description: 'calls per hour allowed by the user', schema: new OAT\Schema(type: 'integer', format: 'int32')),
            ],
            content: new OAT\JsonContent(ref: Product::class)
        ),
        new OAT\Response(response: 401, description: 'oops'),
    ])]
    #[OAT\PathParameter(name: 'product_id', description: 'the product id', schema: new OAT\Schema(type: 'integer'))]
    public function getProduct(?int $product_id)
    {
    }

    /**
     * Add a product.
     *
     * @OA\Post(
     *     path="/products",
     *     tags={"products"},
     *     summary="Add products",
     *     operationId="addProducts",
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     ),
     *     @OA\RequestBody(
     *         description="New product",
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Items(ref="#/components/schemas/Product")
     *         )
     *     )
     * )
     */
    public function addProduct()
    {
    }

    /**
     * Get all.
     */
    #[OAT\Get(path: '/products', operationId: 'getAll', tags: ['products', 'catalog'])]
    #[OAT\Response(
        response: 200,
        description: 'successful operation',
        content: new OAT\JsonContent(
            required: ['data'],
            properties: [
                new OAT\Property(
                    property: 'data',
                    type: 'array',
                    items: new OAT\Items(ref: Product::class)
                ),
            ],
            type: 'object'
        )
    )]
    #[OAT\Response(response: 401, description: 'oops')]
    public function getAll()
    {
    }

    /**
     * @OA\Post(
     *     path="/subscribe",
     *     tags={"products"},
     *     operationId="subscribe",
     *     summary="Subscribe to product webhook",
     *     @OA\Parameter(
     *         name="callbackUrl",
     *         in="query"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="callbackUrl registered"
     *     ),
     *     callbacks={
     *         "onChange": {
     *             "{$request.query.callbackUrl}": {
     *                 "post": {
     *                     "requestBody": @OA\RequestBody(
     *                         description="subscription payload",
     *                         @OA\MediaType(
     *                             mediaType="application/json",
     *                             @OA\Schema(
     *                                 @OA\Property(
     *                                     property="timestamp",
     *                                     description="time of change",
     *                                     type="string",
     *                                     format="date-time"
     *                                 )
     *                             )
     *                         )
     *                     )
     *                 },
     *                 "responses": {
     *                     "200": {
     *                         "description": "Your server implementation should return this HTTP status code if the data was received successfully"
     *                     }
     *                 }
     *             }
     *         }
     *     }
     * )
     */
    public function subscribe()
    {
    }
}
