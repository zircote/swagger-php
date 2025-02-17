<?php

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\UsingTraits\Annotations;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(title="Product model")
 */
class Product
{
    use Colour;
    use BellsAndWhistles;

    /**
     * The unique identifier of a product in our catalog.
     *
     * @var int
     *
     * @OA\Property(format="int64", example=1)
     */
    public $id;

    /**
     * The product bell.
     *
     * @OA\Property(example="gong")
     */
    public $bell;
}
