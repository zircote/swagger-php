<?php

namespace UsingTraits;

/**
 * @OA\Schema(
 *     description="Colour trait",
 *     type="object",
 *     title="Colour trait"
 * )
 */
trait Colour {

    /**
     * The colour.
     *
     * @OA\Property(example="red")
     */
    public $colour;
}
