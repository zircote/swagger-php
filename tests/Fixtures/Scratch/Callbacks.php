<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OA;

/**
 * Callbacks as classic writes them, raw arrays, one in `components` and referenced by pointer.
 *
 * The request body is an annotation inside the raw array, with a schema ref. Hybrid reads the
 * component into a `Callback`, and a reference into one too, so neither the component nor the
 * schema it refers to is removed as unreferenced.
 */
#[OA\Info(title: 'Callbacks', version: '1.0')]
#[OA\Components(callbacks: [
    'productChanged' => [
        '{$request.query.callbackUrl}' => [
            'post' => [
                'operationId' => 'productChanged',
                'requestBody' => new OA\RequestBody(content: [new OA\MediaType(mediaType: 'application/json', schema: new OA\Schema(ref: '#/components/schemas/CallbackPayload'))]),
                'responses' => ['200' => ['description' => 'Received']],
            ],
        ],
    ],
    'productRefunded' => [
        '{$request.body#/refundUrl}' => [
            'post' => ['operationId' => 'productRefunded', 'responses' => ['200' => ['description' => 'Refunded']]],
        ],
    ],
])]
class Callbacks
{
    #[OA\Post(path: '/products/subscribe', operationId: 'subscribe', callbacks: [
        'onChange' => ['$ref' => '#/components/callbacks/productChanged'],
        'onRefund' => ['$ref' => '#/components/callbacks/productRefunded'],
        'onDelete' => [
            '{$request.body#/deleteUrl}' => [
                'delete' => ['operationId' => 'productDeleted', 'responses' => ['204' => ['description' => 'Deleted']]],
            ],
        ],
        'onArchive' => [
            '{$request.body#/archiveUrl}' => [
                'post' => ['operationId' => 'productArchived', 'responses' => ['200' => ['description' => 'Archived']]],
            ],
        ],
    ], responses: [new OA\Response(response: 201, description: 'Subscribed')])]
    public function subscribe()
    {
    }

    #[OA\Post(path: '/orders/subscribe', operationId: 'subscribeOrders', callbacks: [
        'onChange' => ['$ref' => '#/components/callbacks/productChanged'],
    ], responses: [new OA\Response(response: 201, description: 'Subscribed')])]
    public function subscribeOrders()
    {
    }
}

#[OA\Schema(schema: 'CallbackPayload', type: 'object')]
class CallbackPayload
{
}
