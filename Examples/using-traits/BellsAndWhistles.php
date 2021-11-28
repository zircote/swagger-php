<?php

namespace OpenApi\Examples\UsingTraits;

use OpenApi\Examples\UsingTraits\Decoration\Bells;

/**
 * @OA\Schema(title="Bells and Whistles trait")
 */
trait BellsAndWhistles
{
    use Bells;
    use \OpenApi\Examples\UsingTraits\Decoration\Whistles;

    /**
     * The plating.
     *
     * @OA\Property(example="gold")
     */
    public $plating;
}
