<?php

namespace OpenApi\Examples\UsingTraits;

use OpenApi\Annotations as OA;

/**
 * A controller.
 */
class ProductController
{
    use DeleteEntity;

    /**
     * @OA\Get(
     *     tags={"Products"},
     *     path="/products/{product_id}",
     *     @OA\Parameter(
     *         description="ID of product to return",
     *         in="path",
     *         name="product_id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="successful operation",
     *         @OA\JsonContent(
     *             oneOf={
     *           	   @OA\Schema(ref="#/components/schemas/SimpleProduct"),
     *           	   @OA\Schema(ref="#/components/schemas/Product"),
     *           	   @OA\Schema(ref="#/components/schemas/TrickyProduct")
     *             }
     *         )
     *     )
     * )
     */
    public function getProduct($id)
    {
    }
}
