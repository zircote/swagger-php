<?php declare(strict_types=1);

namespace OpenApi\Tests\Fixtures\Apis\DocBlocks;

/**
 * Single file API.
 */

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *   version="1.0.0",
 *   title="Basic single file DocBlocks API"
 * )
 */
class OpenApiSpec
{

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

    /**
     * The id.
     *
     * @OA\Property(format="int64", example=1)
     */
    public $id;
}

class ProductController
{

    /**
     * @OA\Get(
     *   tags={"Products"},
     *   path="/products/{product_id}",
     *   @OA\Response(
     *       response=200,
     *       description="successful operation",
     *       @OA\JsonContent(ref="#/components/schemas/Product")
     *   ),
     *   @OA\Response(
     *       response=401,
     *       description="oops"
     *   )
     * )
     */
    public function getProduct($id)
    {
    }
}