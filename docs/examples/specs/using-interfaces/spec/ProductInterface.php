<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\UsingInterfaces\Spec;

use OpenApi\Spec as OA;

#[OA\Schema]
interface ProductInterface
{
    /**
     * The product name.
     */
    #[OA\Property(property: 'name')]
    #[OA\Schema(example: 'toaster')]
    public function getName();
}
