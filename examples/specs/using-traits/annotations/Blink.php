<?php

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\UsingTraits\Annotations;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(title="Blink trait", schema="CustomName/Blink")
 */
trait Blink
{
    /**
     * The frequency.
     *
     * @OA\Property(example=1)
     */
    public $frequency;
}
