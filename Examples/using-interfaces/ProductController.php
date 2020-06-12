<?php

namespace UsingInterfaces;

class ProductController
{

    /**
     * @OA\Get(
     *   tags={"Products"},
     *   path="/products/{id}",
     *   @OA\Response(
     *       response="default",
     *       description="successful operation",
     *       @OA\JsonContent(ref="#/components/schemas/Product")
     *   )
     * )
     */
    public function getProduct($id)
    {
    }
}
