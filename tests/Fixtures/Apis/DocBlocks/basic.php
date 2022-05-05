<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Apis\DocBlocks;

use OpenApi\Annotations as OA;

/**
 * The Spec.
 *
 * @OA\OpenApi(
 *     security={{"bearerAuth": {}}}
 * )
 *
 * @OA\Info(
 *     version="1.0.0",
 *     title="Basic single file API",
 *     @OA\License(name="MIT", identifier="MIT", @OA\Attachable)
 * )
 * @OA\Server(
 *     url="https://localhost/api",
 *     description="API server"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     description="Basic Auth"
 * )
 * @OA\Tag(
 *     name="products",
 *     description="All about products"
 * )
 * @OA\Tag(
 *     name="catalog",
 *     description="Catalog API"
 * )
 */
class OpenApiSpec
{
}

/**
 * A Server.
 *
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
 * A Colour.
 *
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
 * A Name.
 *
 * @OA\Schema
 */
trait NameTrait
{
    /**
     * The name.
     *
     * @OA\Property
     */
    public $name;
}

/**
 * A Product.
 *
 * @OA\Schema(
 *     title="Product"
 * )
 */
class Product implements ProductInterface
{
    use NameTrait;

    /** @OA\Property */
    public int $quantity;

    /** @OA\Property(nullable=true, default=null, example=null) */
    public string $brand;

    /** @OA\Property */
    public Colour $colour;

    /**
     * The id.
     *
     * @OA\Property(format="int64", example=1)
     */
    public $id;

    /**
     * The kind.
     *
     * @OA\Property(property="kind")
     */
    public const KIND = 'Virtual';

    public function __construct(
        /**
         * @OA\Property(type="string")
         */
        public \DateTimeInterface $releasedAt,
    ) {
    }
}

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
     *         required=false,
     *         description="The product id.",
     *         @OA\Schema(type="int")
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
