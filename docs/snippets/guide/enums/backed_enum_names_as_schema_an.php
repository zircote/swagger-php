<?php

use OpenApi\Annotations as OA;

/**
 * @OA\Schema()
 */
enum Colour: int
{
    case GREEN = 1;
    case BLUE = 2;
    case RED = 3;
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
