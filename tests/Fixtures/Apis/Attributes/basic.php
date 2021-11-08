<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Apis\Attributes;

use OpenApi\Annotations as OA;
use OpenApi\Tests\Annotations as OAF;

#[OA\Info(version: '1.0.0', title: 'Basic single file API', attachables: [new OA\Attachable()])]
#[OA\License(name: 'MIT')]
class OpenApiSpec
{

}

interface ProductInterface
{

}

#[OA\Schema([])]
trait NameTrait
{
    #[OA\Property(description: 'The name.')]
    public $name;

}

#[OA\Schema(title: 'Product', description: 'Product')]
class Product implements ProductInterface
{
    use NameTrait;

    #[OA\Property(description: 'The id.', format: 'int64', example: 1)]
    public $id;
}

class ProductController
{

    #[OA\Get(path: '/products/{product_id}', tags: ['products'], operationId: 'getProducts')]
    #[OA\Response(response: 200, description: 'successful operation', content: new OA\JsonContent(ref: '#/components/schemas/Product'))]
    #[OA\Response(response: 401, description: 'oops')]
    #[OAF\CustomAttachable(value: 'operation')]
    public function getProduct(
        #[OA\PathParameter] string $product_id)
    {
    }


    #[OA\Post(path: '/products', tags: ['products'], operationId: 'addProducts', summary: 'Add products')]
    #[OA\Response(response: 200, description: 'successful operation', content: new OA\JsonContent(ref: '#/components/schemas/Product'))]
    #[OA\RequestBody(required: true, description: 'New product', content: new OA\JsonContent(type: 'array', items: new OA\Items(ref: '#/components/schemas/Product')))]
    public function addProduct()
    {
    }
}