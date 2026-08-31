<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace Fixtures\Scratch;

use OpenApi\Spec as OA;

#[OA\Info(title: 'Http Methods', version: '1.0')]
class HttpMethodsControllerSpec
{
    #[OA\Operation\Head(
        path: '/things',
        operationId: 'headThings',
        responses: [
            new OA\Response(response: 200, description: 'Headers only, no body'),
        ],
    )]
    public function headThings()
    {
    }

    #[OA\Operation\Options(
        path: '/things',
        operationId: 'optionsThings',
        responses: [
            new OA\Response(response: 200, description: 'Allowed methods'),
        ],
    )]
    public function optionsThings()
    {
    }

    #[OA\Operation\Trace(
        path: '/things',
        operationId: 'traceThings',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Echo of the request',
                content: new OA\MediaType(mediaType: 'message/http', schema: new OA\Schema(type: 'string')),
            ),
        ],
    )]
    public function traceThings()
    {
    }
}
