<?php

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\UsingTraits\Annotations;

use OpenApi\Examples\Specs\UsingTraits\Annotations\Blink as TheBlink;
use OpenApi\Annotations as OA;

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
