<?php

namespace UsingTraits;

/**
 * @OA\Schema(
 *     description="Product model",
 *     type="object",
 *     title="Product model"
 * )
 */
class Product {
    use Colour;

    /**
     * The unique identifier of a product in our catalog.
     *
     * @var integer
     * @OA\Property(format="int64", example=1)
     */
    public $id;
}
