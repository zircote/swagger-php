<?php
namespace UsingRefs;

/**
 * @OA\PathItem(
 *   path="/products/{product_id}",
 *   @OA\Parameter(ref="#/components/parameters/product_id_in_path_required")
 * )
 */

class ProductController
{

    /**
     * @OA\Get(
     *   tags={"Products"},
     *   path="/products/{product_id}",
     *   description="Get a product",
     *   operationId="get_product",
     *   @OA\Response(
     *       response=200,
     *       description="successful operation",
     *       ref="#/components/responses/product"
     *   )
     * )
     */
    public function getProduct($id)
    {
    }

    /**
     * @OA\Patch(
     *   tags={"Products"},
     *   path="/products/{product_id}",
     *   description="Patch a product",
     *   operationId="patch_product",
     *   @OA\Parameter(ref="#/components/requestBodies/product_in_body"),
     *   @OA\Response(
     *       response=200,
     *       description="successful operation",
     *       ref="#/components/responses/product"
     *   )
     * )
     */
    public function updateProduct($id)
    {
    }

    /**
     * @OA\Post(
     *   tags={"Products"},
     *   path="/products",
     *   description="Post a product",
     *   operationId="post_product",
     *   @OA\Parameter(ref="#/components/requestBodies/product_in_body"),
     *   @OA\Response(
     *       response=200,
     *       description="successful operation",
     *       ref="#/components/responses/product"
     *   )
     * )
     */
    public function addProduct($id)
    {
    }
}
