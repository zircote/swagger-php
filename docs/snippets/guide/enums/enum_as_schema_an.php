<?php

namespace Openapi\Snippets\Enums\AsSchema;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema()
 */
enum Colour
{
    case GREEN;
    case BLUE;
    case RED;
}

/**
 * @OA\Schema
 */
class Product
{
    /**
     * @OA\Property
     */
    public Colour $colour;
}
