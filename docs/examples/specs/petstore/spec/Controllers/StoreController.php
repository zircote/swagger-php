<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Petstore\Spec\Controllers;

use OpenApi\Examples\Specs\Petstore\Spec\Models\Order;
use OpenApi\Spec as OA;

/**
 * Class Store.
 */
class StoreController
{
    #[OA\Operation\Get(path: '/store', operationId: 'getInventory', summary: 'Returns pet inventories by status', description: 'Returns a map of status codes to quantities', tags: ['store'], responses: [
        new OA\Response(
            response: 200,
            description: 'successful operation',
            content: [
                new OA\MediaType(
                    mediaType: 'application/json',
                    schema: new OA\Schema(additionalProperties: new OA\Schema(type: 'integer', format: 'int32')),
                ),
            ],
        ),
    ], security: [
        new OA\Security\Requirement(scheme: 'api_key', scopes: []),
    ])]
    public function getInventory()
    {
    }

    #[OA\Operation\Post(
        path: '/store/order',
        operationId: 'placeOrder',
        summary: 'Place an order for a pet',
        tags: ['store'],
        requestBody: new OA\RequestBody(
            description: 'order placed for purchasing the pet',
            required: true,
            content: [new OA\MediaType(mediaType: 'application/json', schema: new OA\Schema(ref: Order::class))],
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'successful operation',
                content: [
                    new OA\MediaType(mediaType: 'application/json', schema: new OA\Schema(ref: Order::class)),
                    new OA\MediaType(mediaType: 'application/xml', schema: new OA\Schema(ref: Order::class)),
                ],
            ),
        ],
    )]
    public function placeOrder()
    {
    }

    #[OA\Operation\Get(
        path: '/store/order/{orderId}',
        operationId: 'getOrderById',
        description: 'For valid response try integer IDs with value >= 1 and <= 10. Other values will generated exceptions',
        tags: ['store'],
        parameters: [
            new OA\Parameter(
                name: 'orderId',
                in: 'path',
                description: 'ID of pet that needs to be fetched',
                required: true,
                schema: new OA\Schema(type: 'integer', format: 'int64', minimum: 1, maximum: 10),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'successful operation',
                content: [
                    new OA\MediaType(mediaType: 'application/json', schema: new OA\Schema(ref: Order::class)),
                    new OA\MediaType(mediaType: 'application/xml', schema: new OA\Schema(ref: Order::class)),
                ],
            ),
            new OA\Response(response: 400, description: 'Invalid ID supplied'),
            new OA\Response(response: 404, description: 'Order not found'),
        ],
    )]
    public function getOrderById()
    {
    }

    #[OA\Operation\Delete(
        path: '/store/order/{orderId}',
        operationId: 'deleteOrder',
        summary: 'Delete purchase order by ID',
        description: 'For valid response try integer IDs with positive integer value. Negative or non-integer values will generate API errors',
        tags: ['store'],
        parameters: [
            new OA\Parameter(
                name: 'orderId',
                in: 'path',
                description: 'ID of the order that needs to be deleted',
                required: true,
                schema: new OA\Schema(type: 'integer', format: 'int64', minimum: 1),
            ),
        ],
        responses: [
            new OA\Response(response: 400, description: 'Invalid ID supplied'),
            new OA\Response(response: 404, description: 'Order not found'),
        ],
    )]
    public function deleteOrder()
    {
    }
}
