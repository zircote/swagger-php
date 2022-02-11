<?php

namespace OpenApi\Examples\UsingInterfaces;

class ProductController
{
    /**
     * @OA\Get(
     *     tags={"Products"},
     *     path="/products/{id}",
     *     description="Get product in any colour for id",
     *     tags={"api"},
     *     @OA\Parameter(
     *         description="ID of product to return",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/Product")
     *     )
     * )
     */
    public function getProduct($id)
    {
    }

    /**
     * @OA\Get(
     *     tags={"Products"},
     *     path="/products/green/{id}",
     *     description="Get green products",
     *     tags={"api"},
     *     @OA\Parameter(
     *         description="ID of product to return",
     *         in="path",
     *         name="id",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/GreenProduct")
     *     )
     * )
     */
    public function getGreenProduct($id)
    {
    }
}
