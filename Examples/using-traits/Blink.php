<?php

namespace OpenApi\Examples\UsingTraits;

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
