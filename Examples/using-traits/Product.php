<?php

namespace OpenApi\Examples\UsingTraits;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(title="Product model")
 */
class Product
{
    use \OpenApi\Examples\UsingTraits\Colour;
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
