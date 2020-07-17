<?php

/**
 * Single file API.
 */

namespace OpenApiTests\Fixtures\Apis;

use OpenApi\Annotations as OA;
use OpenApiTests\Annotations as OAT;

/**
 * @OA\Info(
 *   version="1.0.0",
 *   title="Basic single file API",
 * )
 */
class Api
{

}

interface ProductInterface {

}

/**
 * @OA\Schema()
 */
trait NameTrait {
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
     * @OAT\CustomGet(
     *   tags={"Products"},
     *   path="/products/{product_id}",
     *   @OA\Response(
     *       response=200,
     *       description="successful operation",
     *       @OA\JsonContent(ref="#/components/schemas/Product")
     *   )
     * )
     */
    public function getProduct($id)
    {
    }
}
