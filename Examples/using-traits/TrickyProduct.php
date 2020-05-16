<?php

namespace UsingTraits;

use UsingTraits\Blink as TheBlink;

/**
 * @OA\Schema(title="TrickyProduct model")
 * )
 */
class TrickyProduct extends SimpleProduct {
    use Blink; //TheBlink;

    /**
     * The trick.
     *
     * @OA\Property(example="recite poem")
     */
    public $trick;
}
