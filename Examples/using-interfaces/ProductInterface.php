<?php

namespace OpenApi\Examples\UsingInterfaces;

/**
 * @OA\Schema
 */
interface ProductInterface
{
    /**
     * The product name.
     *
     * @OA\Property(property="name", example="toaster")
     */
    public function getName();
}
