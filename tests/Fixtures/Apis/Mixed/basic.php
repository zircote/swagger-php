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
#[OAT\Tag(name: 'products', description: 'All about products')]
#[OAT\Tag(name: 'catalog', description: 'Catalog API')]
#[OAT\Server(url: 'https://localhost/api', description: 'API server')]
class OpenApiSpec
{
}

#[OAT\Server(
    url: 'https://example.localhost',
    description: 'The local environment.'
)]
#[OAT\Server(
    url: 'https://example.com',
    description: 'The production server.'
)]
class Server
{
}

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
     * @OA\Property(format="int64", example=1, @OA\Attachable)
     */
    public $id;

    #[OAT\Property(type: 'string')]
    public \DateTimeInterface $releasedAt;

    #[OAT\Property()]
    public int $quantity;

    #[OAT\Property()]
    public string $brand;

    /** @OA\Property */
    public Colour $colour;
}

class ProductController
{
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
}
