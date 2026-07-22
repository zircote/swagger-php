<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\UsingTraits\Spec;

use OpenApi\Examples\Specs\UsingTraits\Spec\Blink as TheBlink;
use OpenApi\Spec as OA;

#[OA\Schema(title: 'TrickyProduct model')]
class TrickyProduct extends SimpleProduct
{
    use TheBlink;

    /**
     * The trick.
     */
    #[OA\Property]
    #[OA\Schema(example: 'recite poem')]
    public $trick;
}
