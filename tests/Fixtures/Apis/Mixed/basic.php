<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Apis\Mixed;

use OpenApi\Annotations as OA;
use OpenApi\Attributes as OAT;

/**
 * The Spec.
 *
 * @OA\OpenApi(
 *     openapi="3.1.0",
 *     @OA\Info(
 *         version="1.0.0",
 *         title="Basic single file API",
 *         @OA\License(name="MIT", identifier="MIT")
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     description="Basic Auth"
 * )
 */
#[OAT\Server(url: 'https://localhost/api', description: 'API server')]
#[OAT\Tag(name: 'products', description: 'All about products')]
#[OAT\Tag(name: 'catalog', description: 'Catalog API')]
class OpenApiSpec
{
}

#[OAT\Server(
    url: 'https://example.localhost',
    description: 'The local environment.'
)]
/**
 * A Server.
 */
#[OAT\Server(
    url: 'https://example.com',
    description: 'The production server.'
)]
class Server
{
}

/**
 * A Colour.
 */
#[OAT\Schema()]
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
 */
#[OAT\Schema()]
trait NameTrait
{
    /**
     * The name.
     */
    #[OAT\Property()]
    public $name;
}

#[OAT\Schema(title: 'Product', attachables: [new OAT\Attachable()])]
/**
 * A Product.
 */
class Product implements ProductInterface
{
    use NameTrait;

    /**
     * The kind.
     */
    #[OAT\Property(property: 'kind')]
    public const KIND = 'Virtual';

    /**
     * The id.
     *
     * @OA\Property(format="int64", example=1, @OA\Attachable)
     */
    public $id;

    #[OAT\Property(type: 'string')]
    public \DateTimeInterface $releasedAt;

    #[OAT\Property()]
    public int $quantity;

    #[OAT\Property(nullable: true, default: null, example: null)]
    public string $brand;

    /** @OA\Property */
    public Colour $colour;
}

/**
 * The Controller.
 */
class ProductController
{
    /**
     * Get a product.
     */
    #[OAT\Get(
        path: '/products/{product_id}',
        tags: ['products'],
        operationId: 'getProducts',
        responses: [
            new OAT\Response(
                response: 200,
                description: 'successful operation',
                content: new OAT\JsonContent(ref: '#/components/schemas/Product'),
                headers: [
                    new OAT\Header(header: 'X-Rate-Limit', description: 'calls per hour allowed by the user', schema: new OAT\Schema(type: 'integer', format: 'int32')),
                ]
            ),
            new OAT\Response(response: 401, description: 'oops'),
        ],
    )]
    #[OAT\PathParameter(name: 'product_id', required: false, description: 'The product id.', schema: new OAT\Schema(type: 'int'))]
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
     *             type="array",
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
    #[OAT\Get(path: '/products', tags: ['products', 'catalog'], operationId: 'getAll')]
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
                    items: new OAT\Items(ref: '#/components/schemas/Product')
                ),
            ]
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
