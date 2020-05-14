<?php

namespace UsingTraits;

/**
 * @OA\Schema(title="TrickyProduct model")
 * )
 */
class TrickyProduct extends SimpleProduct {
    use Blink;

    /**
     * The trick.
     *
     * @OA\Property(example="recite poem")
     */
    public $trick;
}
