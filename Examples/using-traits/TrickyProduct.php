<?php

namespace OpenApi\Examples\UsingTraits;

use OpenApi\Examples\UsingTraits\Blink as TheBlink;

/**
 * @OA\Schema(title="TrickyProduct model")
 * )
 */
class TrickyProduct extends SimpleProduct
{
    use TheBlink;

    /**
     * The trick.
     *
     * @OA\Property(example="recite poem")
     */
    public $trick;
}
