<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\UsingTraits\Spec\Decoration;

use OpenApi\Spec as OA;

#[OA\Schema(title: 'Bells trait')]
trait Bells
{
    /**
     * The bell (clashes with Product::bell).
     */
    #[OA\Property]
    public string $bell;
}
