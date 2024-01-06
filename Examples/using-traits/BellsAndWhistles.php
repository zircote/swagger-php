<?php

namespace OpenApi\Examples\UsingTraits;

use OpenApi\Examples\UsingTraits\Decoration\Bells;
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
