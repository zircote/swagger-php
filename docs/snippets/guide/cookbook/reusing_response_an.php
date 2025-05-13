<?php

use OpenApi\Annotations as OA;

/**
 * @OA\Response(
 *     response="product",
 *     description="All information about a product",
 *     @OA\JsonContent(ref="#/components/schemas/Product")
 * )
 */
class ProductResponse
{
}

// ...

class ProductController
{
    /**
     * @OA\Get(
     *     tags={"Products"},
     *     path="/products/{product_id}",
     *     @OA\Response(
     *         response="default",
     *         ref="#/components/responses/product"
     *     )
     * )
     */
    public function getProduct($id)
    {
    }
}
