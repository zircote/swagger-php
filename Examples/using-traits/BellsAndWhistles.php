<?php

namespace UsingTraits;

/**
 * @OA\Schema(
 *     description="Bells and Whistles trait",
 *     type="object",
 *     title="Bells and Whistles trait"
 * )
 */
trait BellsAndWhistles {
    use Bells, Whistles;

    /**
     * The plating.
     *
     * @OA\Property(example="gold")
     */
    public $plating;
}
