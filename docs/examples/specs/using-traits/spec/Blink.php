<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\UsingTraits\Spec;

use OpenApi\Spec as OA;

#[OA\Schema(schema: 'CustomName-Blink', title: 'Blink trait')]
trait Blink
{
    /**
     * The frequency.
     */
    #[OA\Property]
    public int $frequency;
}
