<?php

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Petstore\Attributes\Controllers;

use OpenApi\Attributes as OAT;
use OpenApi\Examples\Specs\Petstore\Attributes\Models\Order;

/**
 * Class Store.
 *
 * @author  Donii Sergii <doniysa@gmail.com>
 */
class StoreController
{
    #[OAT\Get(path: '/store', operationId: 'getInventory', description: 'Returns a map of status codes to quantities', summary: 'Returns pet inventories by status', security: [
        [
            'api_key' => [
            ],
        ],
    ], tags: ['store'], responses: [
        new OAT\Response(
            response: 200,
            description: 'successful operation',
            content: new OAT\JsonContent(
                additionalProperties: new OAT\AdditionalProperties(
                    type: 'integer',
                    format: 'int32'
                )
            )
        ),
    ])]
    public function getInventory()
    {
    }

    #[OAT\Post(path: '/store/order', operationId: 'placeOrder', summary: 'Place an order for a pet', requestBody: new OAT\RequestBody(
        description: 'order placed for purchasing the pet',
        required: true,
        content: new OAT\JsonContent(
            ref: Order::class
        )
    ), tags: ['store'], responses: [
        new OAT\Response(
            response: 200,
            description: 'successful operation',
            content: [
                new OAT\JsonContent(
                    ref: Order::class
                ),
                new OAT\XmlContent(
                    ref: Order::class
                ),
            ],
        ),
    ])]
    public function placeOrder()
    {
    }

    #[OAT\Get(path: '/store/order/{orderId}', operationId: 'getOrderById', description: 'For valid response try integer IDs with value >= 1 and <= 10. Other values will generated exceptions', tags: ['store'], parameters: [
        new OAT\Parameter(
            name: 'orderId',
            description: 'ID of pet that needs to be fetched',
            in: 'path',
            required: true,
            schema: new OAT\Schema(
                type: 'integer',
                format: 'int64',
                maximum: 10,
                minimum: 1
            )
        ),
    ], responses: [
        new OAT\Response(
            response: 200,
            description: 'successful operation',
            content: [
                new OAT\JsonContent(
                    ref: Order::class
                ),
                new OAT\XmlContent(
                    ref: Order::class
                ),
            ],
        ),
        new OAT\Response(
            response: 400,
            description: 'Invalid ID supplied'
        ),
        new OAT\Response(
            response: 404,
            description: 'Order not found'
        ),

    ])]
    public function getOrderById()
    {
    }

    #[OAT\Delete(path: '/store/order/{orderId}', operationId: 'deleteOrder', description: 'For valid response try integer IDs with positive integer value. Negative or non-integer values will generate API errors', summary: 'Delete purchase order by ID', tags: ['store'], parameters: [
        new OAT\Parameter(
            name: 'orderId',
            description: 'ID of the order that needs to be deleted',
            in: 'path',
            required: true,
            schema: new OAT\Schema(
                type: 'integer',
                format: 'int64',
                minimum: 1
            )
        ),
    ], responses: [
        new OAT\Response(
            response: 400,
            description: 'Invalid ID supplied'
        ),
        new OAT\Response(
            response: 404,
            description: 'Order not found'
        ),

    ])]
    public function deleteOrder()
    {
    }
}
