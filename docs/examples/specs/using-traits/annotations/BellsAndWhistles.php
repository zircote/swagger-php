<?php

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\UsingTraits\Annotations;

use OpenApi\Examples\Specs\UsingTraits\Annotations\Decoration\Bells;
use OpenApi\Annotations as OA;

/**
 * @OA\Schema(title="Bells and Whistles trait")
 */
trait BellsAndWhistles
{
    use Bells;
    use Decoration\Whistles;

    /**
     * The plating.
     *
     * @OA\Property(example="gold")
     */
    public $plating;
}
