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
class OpenApiSpec
{

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
}

class ProductController
{

    #[OAT\Get(path: '/products/{product_id}', tags: ['products'], operationId: 'getProducts')]
    #[OAT\Response(
        response: 200,
        description: 'successful operation',
        content: new OAT\JsonContent(ref: '#/components/schemas/Product')
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
        content: new OAT\JsonContent(
            type: 'array',
            items: new OAT\Items(ref: '#/components/schemas/Product'))
    )]
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
