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
    #[OAT\Get(
        path: '/store',
        tags: ['store'],
        summary: 'Returns pet inventories by status',
        description: 'Returns a map of status codes to quantities',
        operationId: 'getInventory',
        responses: [
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
        ],
        security: [
            [
                'api_key' => [
                ],
            ],
        ]
    )]
    public function getInventory()
    {
    }

    #[OAT\Post(
        path: '/store/order',
        tags: ['store'],
        summary: 'Place an order for a pet',
        operationId: 'placeOrder',
        responses: [
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
        ],
        requestBody: new OAT\RequestBody(
            description: 'order placed for purchasing the pet',
            required: true,
            content: new OAT\JsonContent(
                ref: Order::class
            )
        )
    )]
    public function placeOrder()
    {
    }

    #[OAT\Get(
        path: '/store/order/{orderId}',
        tags: ['store'],
        description: 'For valid response try integer IDs with value >= 1 and <= 10. Other values will generated exceptions',
        operationId: 'getOrderById',
        parameters: [
            new OAT\Parameter(
                name: 'orderId',
                in: 'path',
                description: 'ID of pet that needs to be fetched',
                required: true,
                schema: new OAT\Schema(
                    type: 'integer',
                    format: 'int64',
                    minimum: 1,
                    maximum: 10
                )
            ),
        ],
        responses: [
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

        ],
    )]
    public function getOrderById()
    {
    }

    #[OAT\Delete(
        path: '/store/order/{orderId}',
        tags: ['store'],
        summary: 'Delete purchase order by ID',
        description: 'For valid response try integer IDs with positive integer value. Negative or non-integer values will generate API errors',
        operationId: 'deleteOrder',
        parameters: [
            new OAT\Parameter(
                name: 'orderId',
                in: 'path',
                description: 'ID of the order that needs to be deleted',
                required: true,
                schema: new OAT\Schema(
                    type: 'integer',
                    format: 'int64',
                    minimum: 1
                )
            ),
        ],
        responses: [
            new OAT\Response(
                response: 400,
                description: 'Invalid ID supplied'
            ),
            new OAT\Response(
                response: 404,
                description: 'Order not found'
            ),

        ],
    )]
    public function deleteOrder()
    {
    }
}
