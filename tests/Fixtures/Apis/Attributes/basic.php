<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Apis\Attributes;

use OpenApi\Attributes as OAT;
use OpenApi\Tests\Fixtures\Attributes as OAF;

#[OAT\Info(
    version: '1.0.0',
    title: 'Basic single file API',
    attachables: [new OAT\Attachable()]
)]
#[OAT\License(name: 'MIT', identifier: 'MIT')]
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
    #[OAT\Property(description: 'The name.')]
    public $name;

}

#[OAT\Schema(title: 'Product', description: 'Product')]
class Product implements ProductInterface
{
    use NameTrait;

    #[OAT\Property(description: 'The id.', format: 'int64', example: 1)]
    public $id;

    public function __construct(
        #[OAT\Property()] public int $quantity,
        #[OAT\Property()] public string $brand,
        #[OAT\Property()] public Colour $colour,
        #[OAT\Property(type: 'string')] public \DateTimeInterface $releasedAt,
    )
    {
    }
}

class ProductController
{

    #[OAT\Get(path: '/products/{product_id}', tags: ['products'], operationId: 'getProducts')]
    #[OAT\Response(
        response: 200,
        description: 'successful operation',
        content: [new OAT\MediaType(mediaType: 'application/json', schema: new OAT\Schema(ref: '#/components/schemas/Product'))],
        headers: [
            new OAT\Header(header: 'X-Rate-Limit', description: 'calls per hour allowed by the user', schema: new OAT\Schema(type: 'integer', format: 'int32')),
        ]
    )]
    #[OAT\Response(response: 401, description: 'oops')]
    #[OAF\CustomAttachable(value: 'operation')]
    public function getProduct(
        #[OAT\PathParameter(description: 'The product id.')] int $product_id)
    {
    }

    #[OAT\Post(path: '/products', tags: ['products'], operationId: 'addProducts', summary: 'Add products')]
    #[OAT\Response(
        response: 200,
        description: 'successful operation',
        content: new OAT\JsonContent(ref: '#/components/schemas/Product')
    )]
    #[OAT\RequestBody(
        required: true,
        description: 'New product',
        content: [new OAT\MediaType(
            mediaType: 'application/json',
            schema: new OAT\Schema(
                type: 'array',
                items: new OAT\Items(type: Product::class)
            )
        )]
    )]
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
                    items: new OAT\Items(ref: '#/components/schemas/Product'))
            ])
    )]
    #[OAT\Response(response: 401, description: 'oops')]
    public function getAll()
    {
    }
}
