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
#[OAT\Schema(
    description: 'Order model',
    title: 'Order model'
)]
class Order
{
    #[OAT\Property(
        description: 'ID',
        title: 'ID',
        format: 'int64',
        default: 1
    )]
    private int $id;

    #[OAT\Property(
        description: 'Pet ID',
        title: 'Pet ID',
        format: 'int64',
        default: 1
    )]
    private int $petId;

    #[OAT\Property(
        description: 'Quantity',
        title: 'Quantity',
        format: 'int32',
        default: 12
    )]
    private int $quantity;

    #[OAT\Property(
        description: 'Shipping date',
        title: 'Shipping date',
        format: 'datetime',
        type: 'string',
        default: '2017-02-02 18:31:45'
    )]
    private \DateTime $shipDate;

    #[OAT\Property(
        description: 'Order status',
        title: 'Order status',
        enum: ['placed', 'approved', 'delivered'],
        default: 'placed'
    )]
    private string $status;

    #[OAT\Property(
        description: 'Complete status',
        title: 'Complete status',
        type: 'boolean',
        default: false
    )]
    private bool $complete;
}
