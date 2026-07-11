<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\UsingTraits\Spec;

use OpenApi\Spec as OA;

#[OA\Schema(title: 'Product model')]
class Product
{
    use Colour;
    use BellsAndWhistles;

    /**
     * The unique identifier of a product in our catalog.
     */
    #[OA\Property]
    #[OA\Schema(type: 'integer', format: 'int64', example: 1)]
    public int $id;

    /**
     * The product bell.
     */
    #[OA\Property]
    #[OA\Schema(example: 'gong')]
    public string $bell;
}
