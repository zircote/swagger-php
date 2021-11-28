<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Apis\Mixed;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *   version="1.0.0",
 *   title="Basic single file API",
 *   @OA\License(name="MIT")
 * )
 */
class OpenApiSpec
{

}

interface ProductInterface
{

}

#[OA\Schema([])]
trait NameTrait
{
    /**
     * The name.
     */
    #[OA\Property()]
    public $name;

}

#[OA\Schema(title: 'Product', description: 'Product', attachables: [new OA\Attachable()])]
class Product implements ProductInterface
{
    use NameTrait;

    /**
     * The id.
     *
     * @OA\Property(format="int64", example=1, @OA\Attachable())
     */
    public $id;
}

class ProductController
{

    #[OA\Get(
        path: '/products/{product_id}',
        tags: ['products'],
        operationId: 'getProducts',
        responses: [
            new OA\Response(response: 200, description: 'successful operation', content: new OA\JsonContent(ref: '#/components/schemas/Product')),
            new OA\Response(response: 401, description: 'oops'),
        ],
    )]
    #[OA\PathParameter(name: 'product_id', schema: new OA\Schema(type: 'string'))]
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