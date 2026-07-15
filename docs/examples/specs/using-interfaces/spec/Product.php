<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\UsingInterfaces\Spec;

use OpenApi\Spec as OA;

#[OA\Schema(title: 'Product model')]
class Product implements ProductInterface
{
    /**
     * The unique identifier of a product in our catalog.
     */
    #[OA\Property(property: 'id')]
    #[OA\Schema(type: 'integer', format: 'int64', example: 1)]
    public int $id;

    public function getName(): string
    {
        return 'kettle';
    }
}
