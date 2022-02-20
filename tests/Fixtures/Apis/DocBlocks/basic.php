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
 *   @OA\License(name="MIT", identifier="MIT", @OA\Attachable())
 * )
 * @OA\Server(
 *   url="https://localhost/api",
 *   description="API server"
 * )
 * @OA\Tag(
 *   name="products",
 *   description="All about products"
 * )
 * @OA\Tag(
 *   name="catalog",
 *   description="Catalog API"
 * )
 */
class OpenApiSpec
{

}

/**
 * @OA\Server(
 *     url="https://example.localhost",
 *     description="The local environment."
 * )
 * @OA\Server(
 *     url="https://example.com",
 *     description="The production server."
 * )
 */
class Server
{
}

/**
 * @OA\Schema()
 */
enum Colour
{
    case GREEN;
    case BLUE;
    case RED;
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

    /** @OA\Property() */
    public int $quantity;

    /** @OA\Property() */
    public string $brand;

    /** @OA\Property() */
    public Colour $colour;

    /**
     * The id.
     *
     * @OA\Property(format="int64", example=1)
     */
    public $id;

    public function __construct(
        /**
         * @OA\Property(type="string")
         */
        public \DateTimeInterface $releasedAt,
    )
    {
    }
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
     *     description="The product id.",
     *     @OA\Schema(type="int")
     *   ),
     *   @OA\Response(
     *       response=200,
     *       description="successful operation",
     *       @OA\Header(
     *           header="X-Rate-Limit",
     *           description="calls per hour allowed by the user",
     *           @OA\Schema(
     *               type="integer",
     *               format="int32"
     *           )
     *       ),
     *       @OA\MediaType(mediaType="application/json", @OA\Schema(ref="#/components/schemas/Product"))
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="oops"
     *   )
     * )
     */
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
     * @OA\Get(
     *   tags={"products", "catalog"},
     *   path="/products",
     *   operationId="getAll",
     *   @OA\Response(
     *       response=200,
     *       description="successful operation",
     *       @OA\JsonContent(
     *           type="object",
     *           required={"data"},
     *           @OA\Property(
     *               property="data",
     *               type="array",
     *               @OA\Items(ref="#/components/schemas/Product")
     *           )
     *       )
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="oops"
     *   )
     * )
     */
    public function getAll()
    {
    }
}
