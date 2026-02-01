<?php

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Petstore\Attributes\Models;

use OpenApi\Attributes as OAT;

/**
 * Class Order.
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 */
#[OAT\Schema(title: 'Order model', description: 'Order model')]
class Order
{
    #[OAT\Property(title: 'ID', description: 'ID', format: 'int64', default: 1)]
    private int $id;

    #[OAT\Property(title: 'Pet ID', description: 'Pet ID', format: 'int64', default: 1)]
    private int $petId;

    #[OAT\Property(title: 'Quantity', description: 'Quantity', format: 'int32', default: 12)]
    private int $quantity;

    #[OAT\Property(title: 'Shipping date', description: 'Shipping date', type: 'string', format: 'datetime', default: '2017-02-02 18:31:45')]
    private \DateTime $shipDate;

    #[OAT\Property(title: 'Order status', description: 'Order status', default: 'placed', enum: ['placed', 'approved', 'delivered'])]
    private string $status;

    #[OAT\Property(title: 'Complete status', description: 'Complete status', type: 'boolean', default: false)]
    private bool $complete;
}
