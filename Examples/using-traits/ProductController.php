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
     *   @OA\Parameter(
     *     name="product_id",
     *     in="query",
     *     description="Product id",
     *     required=false,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="successful operation"
     *   )
     * )
     */
    public function getProduct($id)
    {
    }
}
