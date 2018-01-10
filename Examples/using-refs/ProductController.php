<?php
namespace UsingRefs;

/**
 * @OAS\PathItem(
 *   path="/products/{product_id}",
 *   @OAS\Parameter(ref="#/components/parameters/product_id_in_path_required")
 * )
 */

class ProductController {

    /**
     * @OAS\Get(
     *   tags={"Products"},
     *   path="/products/{product_id}",
     *   @OAS\Response(
     *       response="default",
     *       description="successful operation",
     *       @OAS\MediaType(
     *          mediaType="application/json",
     *          @OAS\Schema(
     *            ref="#/components/responses/product"
     *          )
     *       )
     *   )
     * )
     */
    public function getProduct($id) {

    }

    /**
     * @OAS\Patch(
     *   tags={"Products"},
     *   path="/products/{product_id}",
     *   @OAS\Parameter(ref="#/components/requestBodies/product_in_body"),
     *   @OAS\Response(
     *       response="default",
     *       description="successful operation",
     *       @OAS\MediaType(
     *          mediaType="application/json",
     *          @OAS\Schema(
     *            ref="#/components/responses/product"
     *          )
     *       )
     *   )
     * )
     */
    public function updateProduct($id) {

    }

    /**
     * @OAS\Post(
     *   tags={"Products"},
     *   path="/products",
     *   @OAS\Parameter(ref="#/components/requestBodies/product_in_body"),
     *   @OAS\Response(
     *       response="default",
     *       description="successful operation",
     *       @OAS\MediaType(
     *          mediaType="application/json",
     *          @OAS\Schema(
     *            ref="#/components/responses/product"
     *          )
     *       )
     *   )
     * )
     */
    public function addProduct($id) {

    }

}