<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Apis\Attributes;

use OpenApi\Attributes as OAT;
use OpenApi\Tests\Fixtures\Attributes as OAF;

/**
 * The Spec.
 */
#[OAT\OpenApi(openapi: '3.1.0', security: [['bearerAuth' => []]])]
#[OAT\Info(
    version: '1.0.0',
    title: 'Basic single file API',
    attachables: [new OAT\Attachable()]
)]
#[OAT\License(name: 'MIT', identifier: 'MIT')]
#[OAT\Server(url: 'https://localhost/api', description: 'API server')]
#[OAT\SecurityScheme(securityScheme: 'bearerAuth', type: 'http', scheme: 'bearer', description: 'Basic Auth')]
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
/**
 * A Server.
 */
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
    #[OAT\Property(description: 'The name.')]
    public $name;
}

/**
 * A Product description ignored.
 */
#[OAT\Schema(title: 'Product', description: 'A Product.')]
class Product implements ProductInterface
{
    use NameTrait;

    /**
     * The kind.
     */
    #[OAT\Property(property: 'kind')]
    public const KIND = 'Virtual';

    #[OAT\Property(description: 'The id.', format: 'int64', example: 1)]
    /**
     * The id.
     */
    public $id;

    public function __construct(
        #[OAT\Property()]
        public int $quantity,
        #[OAT\Property(default: null, example: null)]
        public ?string $brand,
        #[OAT\Property()]
        public Colour $colour,
        #[OAT\Property(type: 'string')]
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
     * @param $product_id the product id
     */
    #[OAT\Get(path: '/products/{product_id}', tags: ['products'], operationId: 'getProducts')]
    #[OAT\Response(
        response: 200,
        description: 'successful operation',
        content: [new OAT\MediaType(mediaType: 'application/json', schema: new OAT\Schema(ref: Product::class))],
        headers: [
            new OAT\Header(header: 'X-Rate-Limit', description: 'calls per hour allowed by the user', schema: new OAT\Schema(type: 'integer', format: 'int32')),
        ]
    )]
    #[OAT\Response(response: 401, description: 'oops')]
    #[OAF\CustomAttachable(value: 'operation')]
    public function getProduct(
        #[OAT\PathParameter]
        ?int $product_id
    ) {
    }

    #[OAT\Post(path: '/products', tags: ['products'], operationId: 'addProducts', summary: 'Add products')]
    #[OAT\Response(
        response: 200,
        description: 'successful operation',
        content: new OAT\JsonContent(ref: Product::class)
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
    /**
     * Add a product.
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
                    items: new OAT\Items(ref: Product::class)
                ),
            ]
        )
    )]
    /**
     * Get all.
     */
    #[OAT\Response(response: 401, description: 'oops')]
    public function getAll()
    {
    }

    #[OAT\Post(
        path: '/subscribe',
        operationId: 'subscribe',
        summary: 'Subscribe to product webhook',
        tags: ['products'],
        callbacks: [
            'onChange' => [
                '{$request.query.callbackUrl}' => [
                    'post' => [
                        'requestBody' => new OAT\RequestBody(
                            description: 'subscription payload',
                            content: [
                                new OAT\MediaType(
                                    mediaType: 'application/json',
                                    schema: new OAT\Schema(
                                        properties: [
                                            new OAT\Property(
                                                property: 'timestamp',
                                                description: 'time of change',
                                                type: 'string',
                                                format: 'date-time'
                                            ),
                                        ]
                                    )
                                ),
                            ]
                        ),
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Your server implementation should return this HTTP status code if the data was received successfully',
                        ],
                    ],
                ],

            ],
        ]
    )]
    #[OAT\Parameter(
        name: 'callbackUrl',
        in: 'query'
    )]
    #[OAT\Response(
        response: 200,
        description: 'callbackUrl registered'
    )]
    public function subscribe()
    {
    }
}
