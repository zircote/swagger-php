<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

/**
 * Callbacks as `Callback` attributes: one declared as a component and referenced, by class and by
 * pointer, one inline, and the raw array beside them. One more is declared on a method of a
 * `Components` holder without a name, and is named after the method.
 */
#[OA\Info(title: 'Callbacks', version: '1.0')]
#[OA\Callback(component: 'productChanged', expression: '{$request.query.callbackUrl}', operations: [
    new OA\Operation\Post(
        operationId: 'productChanged',
        requestBody: new OA\RequestBody(content: new OA\MediaType\Json(ref: CallbackPayloadSpec::class)),
        responses: [new OA\Response(response: 200, description: 'Received')],
    ),
])]
class CallbacksSpec
{
    #[OA\Operation\Post(path: '/products/subscribe', operationId: 'subscribe', callbacks: [
        'onChange' => new OA\Callback(ref: CallbacksSpec::class),
        'onRefund' => new OA\Callback(ref: '#/components/callbacks/productRefunded'),
        'onDelete' => new OA\Callback(expression: '{$request.body#/deleteUrl}', operations: [
            new OA\Operation\Delete(operationId: 'productDeleted', responses: [new OA\Response(response: 204, description: 'Deleted')]),
        ]),
        'onArchive' => [
            '{$request.body#/archiveUrl}' => [
                'post' => ['operationId' => 'productArchived', 'responses' => ['200' => ['description' => 'Archived']]],
            ],
        ],
    ])]
    #[OA\Response(response: 201, description: 'Subscribed')]
    public function subscribe()
    {
    }

    #[OA\Operation\Post(path: '/orders/subscribe', operationId: 'subscribeOrders', callbacks: [
        'onChange' => new OA\Callback(ref: '#/components/callbacks/productChanged'),
    ])]
    #[OA\Response(response: 201, description: 'Subscribed')]
    public function subscribeOrders()
    {
    }
}

#[OA\Components]
class CallbacksHolderSpec
{
    #[OA\Callback(expression: '{$request.body#/refundUrl}', operations: [
        new OA\Operation\Post(operationId: 'productRefunded', responses: [new OA\Response(response: 200, description: 'Refunded')]),
    ])]
    public function productRefunded()
    {
    }
}

#[OA\Schema(component: 'CallbackPayload', type: 'object')]
class CallbackPayloadSpec
{
}
