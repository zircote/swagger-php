<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Apis\Mixed;

use OpenApi\Annotations as OA;
use OpenApi\Attributes as OAT;

/**
 * @OA\Info(
 *   version="1.0.0",
 *   title="Basic single file API",
 *   @OA\License(name="MIT", identifier="MIT")
 * )
 */
class OpenApiSpec
{

}

interface ProductInterface
{

}

#[OAT\Schema()]
trait NameTrait
{
    /**
     * The name.
     */
    #[OAT\Property()]
    public $name;

}

#[OAT\Schema(title: 'Product', description: 'Product', attachables: [new OAT\Attachable()])]
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

    #[OAT\Get(
        path: '/products/{product_id}',
        tags: ['products'],
        operationId: 'getProducts',
        responses: [
            new OAT\Response(response: 200, description: 'successful operation', content: new OAT\JsonContent(ref: '#/components/schemas/Product')),
            new OAT\Response(response: 401, description: 'oops'),
        ],
    )]
    #[OAT\PathParameter(name: 'product_id', description: 'The product id.', schema: new OAT\Schema(type: 'int'))]
    public function getProduct(int $product_id)
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

    #[OAT\Get(path: '/products', tags: ['products'], operationId: 'getAll')]
    #[OAT\Response(
        response: 200,
        description: 'successful operation',
        content: new OAT\JsonContent(
            type: 'object',
            required: ['data'],
            properties: [
                new OAT\Property(
                    property: 'data',
                    type: 'array',
                    items: new OAT\Items(ref: '#/components/schemas/Product'))
            ])
    )]
    #[OAT\Response(response: 401, description: 'oops')]
    public function getAll()
    {
    }
}
