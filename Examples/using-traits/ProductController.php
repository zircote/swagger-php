<?php

namespace UsingTraits;

/**
 * A controller.
 */
class ProductController
{
    use DeleteEntity;

    /**
     * @OA\Get(
     *   tags={"Products"},
     *   path="/products/{product_id}",
     *   description="Get a product",
     *   @OA\Parameter(
     *     name="product_id",
     *     in="path",
     *     description="Product id",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="successful operation"
     *   )
     * )
     */
    public function getProduct($id)
    {
    }
}
