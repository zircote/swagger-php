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
