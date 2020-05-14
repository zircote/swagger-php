<?php

namespace UsingInterfaces;

/**
 * Product interface.
 *
 * @OA\Schema()
 */
interface ProductInterface {

    /**
     * The name of the product.
     *
     * @return string
     * @example Toaster
     * @OA\Property(property="name")
     */
    public function getName();
}
