<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\UsingRefs\Spec;

use OpenApi\Spec as OA;

#[OA\Schema(title: 'Product model', description: 'Product model', type: 'object')]
class Product extends Model
{
    /**
     * The unique identifier of a product in our catalog.
     */
    #[OA\Property]
    #[OA\Schema(type: 'integer', format: 'int64', example: 1)]
    public int $id;

    #[OA\Property(schema: new OA\Schema(ref: '#/components/schemas/product_status'))]
    public string $status;

    #[OA\Property]
    public StockLevel $stockLevel;
}
