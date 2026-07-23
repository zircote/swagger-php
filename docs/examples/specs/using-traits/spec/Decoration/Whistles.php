<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\UsingTraits\Spec\Decoration;

use OpenApi\Spec as OA;

#[OA\Schema(title: 'Whistles trait')]
trait Whistles
{
    /**
     * The bell.
     */
    #[OA\Property]
    #[OA\Schema(example: 'bone whistle')]
    public $whistle;
}
