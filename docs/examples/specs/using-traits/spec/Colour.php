<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\UsingTraits\Spec;

use OpenApi\Spec as OA;

#[OA\Schema(title: 'Colour trait')]
trait Colour
{
    /**
     * The colour.
     */
    #[OA\Property]
    public string $colour;
}
