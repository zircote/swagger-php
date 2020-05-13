<?php

namespace UsingTraits;

/**
 * @OA\Schema(
 *     description="Whistles trait",
 *     type="object",
 *     title="Whistles trait"
 * )
 */
trait Whistles {

    /**
     * The bell.
     *
     * @OA\Property(example="bone whistle")
     */
    public $whistle;
}
