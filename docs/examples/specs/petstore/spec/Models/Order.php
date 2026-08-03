<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Petstore\Spec\Models;

use OpenApi\Spec as OA;

/**
 * Class Order.
 */
#[OA\Schema(title: 'Order model', description: 'Order model')]
class Order
{
    protected string $unrelated;

    #[OA\Property]
    #[OA\Schema(title: 'ID', description: 'ID', format: 'int64', default: 1)]
    private int $id;

    #[OA\Property]
    #[OA\Schema(title: 'Pet ID', description: 'Pet ID', format: 'int64', default: 1)]
    private int $petId;

    #[OA\Property]
    #[OA\Schema(title: 'Quantity', description: 'Quantity', format: 'int32', default: 12)]
    private int $quantity;

    #[OA\Property]
    #[OA\Schema(title: 'Shipping date', description: 'Shipping date', format: 'datetime', default: '2017-02-02 18:31:45')]
    private \DateTime $shipDate;

    #[OA\Property]
    #[OA\Schema(title: 'Order status', description: 'Order status', enum: ['placed', 'approved', 'delivered'], default: 'placed')]
    private string $status;

    #[OA\Property]
    #[OA\Schema(title: 'Complete status', description: 'Complete status', default: false)]
    private bool $complete;
}
