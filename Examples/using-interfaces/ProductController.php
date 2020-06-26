<?php

namespace UsingInterfaces;

class ProductController
{

    /**
     * @OA\Get(
     *   tags={"Products"},
     *   path="/products/{id}",
     *   description="Get product",
     *   @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="Product id",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
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
