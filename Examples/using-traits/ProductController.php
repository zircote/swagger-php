<?php

namespace UsingTraits;

/**
 * @OA\PathItem(path="/products")
 */
class ProductController
{

    /**
     * @OA\Get(
     *   tags={"Products"},
     *   path="/products/{product_id}",
     *   @OA\Response(
     *       response="default",
     *       description="successful operation"
     *   )
     * )
     */
    public function getProduct($id)
    {
    }
}
