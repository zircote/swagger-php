<?php
namespace UsingRefs;

/**
 * @SWG\Path(
 *   path="/products/{product_id}",
 *   @SWG\Parameter(ref="#/parameters/product_id_in_path_required")
 * )
 */

class ProductController {

    /**
     * @SWG\Get(
     *   tags={"Products"},
     *   path="/products/{product_id}",
     *   @SWG\Response(response="default", ref="#/responses/product")
     * )
     */
    public function getProduct($id) {

    }

    /**
     * @SWG\Patch(
     *   tags={"Products"},
     *   path="/products/{product_id}",
     *   @SWG\Parameter(ref="#/parameters/product_in_body"),
     *   @SWG\Response(response="default", ref="#/responses/product")
     * )
     */
    public function updateProduct($id) {

    }

    /**
     * @SWG\Post(
     *   tags={"Products"},
     *   path="/products",
     *   @SWG\Parameter(ref="#/parameters/product_in_body"),
     *   @SWG\Response(response="default", ref="#/responses/product")
     * )
     */
    public function addProduct($id) {

    }

}