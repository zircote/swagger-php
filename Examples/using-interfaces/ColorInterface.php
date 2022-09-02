<?php

namespace OpenApi\Examples\UsingInterfaces;

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
