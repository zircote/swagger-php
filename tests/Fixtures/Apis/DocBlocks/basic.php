<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Apis\DocBlocks;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *   version="1.0.0",
 *   title="Basic single file API",
 *   @OA\License(name="MIT", @OA\Attachable())
 * )
 */
class OpenApiSpec
{

}

interface ProductInterface
{

}

/**
 * @OA\Schema()
 */
trait NameTrait
{
    /**
     * The name.
     *
     * @OA\Property()
     */
    public $name;

}

/**
 * @OA\Schema(
 *     description="Product",
 *     title="Product"
 * )
 */
class Product implements ProductInterface
{
    use NameTrait;

    /**
     * The id.
     *
     * @OA\Property(format="int64", example=1)
     */
    public $id;
}

class ProductController
{

    /**
     * @OA\Get(
     *   tags={"products"},
     *   path="/products/{product_id}",
     *   operationId="getProducts",
     *   @OA\PathParameter(
     *     name="product_id",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *       response=200,
     *       description="successful operation",
     *       @OA\JsonContent(ref="#/components/schemas/Product")
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="oops"
     *   )
     * )
     */
    public function getProduct(string $product_id)
    {
    }

    /**
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
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Product")
     *         )
     *     )
     * )
     */
    public function addProduct()
    {
    }
}