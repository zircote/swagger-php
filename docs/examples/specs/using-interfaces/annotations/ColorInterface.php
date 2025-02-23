<?php

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\UsingInterfaces\Annotations;

use OpenApi\Annotations as OA;

/**
 * Not a schema.
 */
interface ColorInterface
{
    /**
     * The product color.
     *
     * @OA\Property(property="color", example="blue")
     */
    public function getColor();
}
