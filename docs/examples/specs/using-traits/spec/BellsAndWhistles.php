<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\UsingTraits\Spec;

use OpenApi\Spec as OA;

#[OA\Schema(title: 'Bells and Whistles trait')]
trait BellsAndWhistles
{
    use Decoration\Bells;
    use Decoration\Whistles;

    /**
     * The plating.
     */
    #[OA\Property]
    public string $plating;
}
