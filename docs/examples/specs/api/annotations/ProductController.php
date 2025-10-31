<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Api\Annotations;

use OpenApi\Annotations as OA;

/**
 * The Controller.
 */
class ProductController
{
    /**
     * Get a product.
     *
     * @OA\Get(
     *     tags={"products"},
     *     path="/products/{product_id}",
     *     operationId="getProducts",
     *     @OA\PathParameter(
     *         name="product_id",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\Header(
     *             header="X-Rate-Limit",
     *             description="calls per hour allowed by the user",
     *             @OA\Schema(
     *                 type="integer",
     *                 format="int32"
     *             )
     *         ),
     *         @OA\MediaType(mediaType="application/json", @OA\Schema(ref="#/components/schemas/Product"))
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="oops"
     *     )
     * )
     *
     * @param ?int $product_id the product id
     */
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
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Product")
     *             )
     *         )
     *     )
     * )
     */
    public function addProduct()
    {
    }

    /**
     * Get all.
     *
     * @OA\Get(
     *     tags={"products", "catalog"},
     *     path="/products",
     *     operationId="getAll",
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(
     *             type="object",
     *             required={"data"},
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Product")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="oops"
     *     )
     * )
     */
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
