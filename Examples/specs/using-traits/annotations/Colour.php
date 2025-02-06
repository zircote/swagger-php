<?php

namespace OpenApi\Examples\Specs\UsingTraits\Annotations;

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
