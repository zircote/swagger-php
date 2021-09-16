<?php declare(strict_types=1);

/**
 * Single file API using PHP8 attributes.
 */

namespace OpenApi\Tests\Fixtures\Apis\Attributes;

use OpenApi\Annotations as OA;

#[OA\Info(version: '1.0.0', title: 'Basic single file API')]
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

    #[OA\Get(
        path: '/products/{product_id}',
        tags: ['Products'],
        operationId: 'getProducts',
        responses: [
            new OA\Response(response: 200, description: 'successful operation', content: new OA\JsonContent(ref: '#/components/schemas/Product')),
            new OA\Response(response: 401, description: 'oops'),
        ]
    )]
    public function getProduct($id)
    {
    }
}