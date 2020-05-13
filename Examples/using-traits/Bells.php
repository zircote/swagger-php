<?php

namespace UsingTraits;

/**
 * @OA\Schema(title="Bells trait")
 */
trait Bells {

    /**
     * The bell.
     *
     * @OA\Property(example="chime")
     */
    public $bell;
}
