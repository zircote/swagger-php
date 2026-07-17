<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\UsingTraits\Spec;

use OpenApi\Spec as OA;

#[OA\Schema(title: 'SimpleProduct model')]
class SimpleProduct
{
    use Decoration\Bells;
    use Decoration\UndocumentedBell;

    /**
     * The unique identifier of a simple product in our catalog.
     */
    #[OA\Property]
    #[OA\Schema(type: 'integer', format: 'int64', example: 1)]
    public int $id;
}
