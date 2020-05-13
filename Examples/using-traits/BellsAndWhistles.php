<?php

namespace UsingTraits;

/**
 * @OA\Schema(title="Bells and Whistles trait")
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
