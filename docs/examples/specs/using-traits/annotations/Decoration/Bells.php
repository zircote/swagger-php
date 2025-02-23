<?php

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\UsingTraits\Annotations\Decoration;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(title="Bells trait")
 */
trait Bells
{
    /**
     * The bell (clashes with Product::bell).
     *
     * @OA\Property(example="chime")
     */
    public $bell;
}
