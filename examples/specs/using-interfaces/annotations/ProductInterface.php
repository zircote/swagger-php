<?php

namespace OpenApi\Examples\Specs\UsingInterfaces\Annotations;

use OpenApi\Annotations as OA;

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
