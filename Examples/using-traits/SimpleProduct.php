<?php

namespace UsingTraits;

/**
 * @OA\Schema(title="SimpleProduct model")
 * )
 */
class SimpleProduct {
    use Bells;

    /**
     * The unique identifier of a simple product in our catalog.
     *
     * @var integer
     * @OA\Property(format="int64", example=1)
     */
    public $id;
}
