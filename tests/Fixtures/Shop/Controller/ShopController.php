<?php

namespace SwaggerTests\Fixtures\Shop\Controller;

use Swagger\Annotations as SWG;

/**
 * @SWG\Swagger(
 *      schemes={"http"},
 *      host="example.com",
 *      basePath="/api",
 *      @SWG\Info(
 *          title="Shop",
 *          version="3.7"
 *      )
 * )
 */
class ShopController
{
    /**
     * @SWG\Get(
     *      path="/products/{product}",
     *      @SWG\Parameter(
     *          name="product",
     *          in="path",
     *          type="string",
     *          description="Product number"
     *      ),
     *      @SWG\Response(
     *          response="200",
     *          description="Product information",
     *          @SWG\Schema(ref="#/definitions/Product")
     *      )
     * )
     */
    public function getProduct($product)
    {
    }

    /**
     * @SWG\Post(
     *      path="/products",
     *      @SWG\Parameter(
     *          name="product",
     *          in="body",
     *          @SWG\Schema(ref="#/definitions/Product")
     *      ),
     *      @SWG\Response(
     *          response="200",
     *          description="Product information",
     *          @SWG\Schema(ref="#/definitions/Product")
     *      )
     * )
     */
    public function addProduct()
    {
    }
}
