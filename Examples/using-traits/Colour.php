<?php

namespace OpenApi\Examples\UsingTraits;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(title="Colour trait")
 */
trait Colour
{
    /**
     * The colour.
     *
     * @OA\Property(example="red")
     */
    public $colour;
}
