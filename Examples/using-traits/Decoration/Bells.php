<?php

namespace OpenApi\Examples\UsingTraits\Decoration;

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
