<?php

namespace UsingTraits;

/**
 * @OA\Schema(
 *     description="Bells trait",
 *     type="object",
 *     title="Bells trait"
 * )
 */
trait Bells {

    /**
     * The bell.
     *
     * @OA\Property(example="chime")
     */
    public $bell;
}
