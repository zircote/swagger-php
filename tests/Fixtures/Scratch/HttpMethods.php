<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

#[OAT\Info(title: 'Http Methods', version: '1.0')]
class HttpMethodsController
{
    #[OAT\Head(
        path: '/things',
        operationId: 'headThings',
        responses: [
            new OAT\Response(response: 200, description: 'Headers only, no body'),
        ],
    )]
    public function headThings()
    {
    }

    #[OAT\Options(
        path: '/things',
        operationId: 'optionsThings',
        responses: [
            new OAT\Response(response: 200, description: 'Allowed methods'),
        ],
    )]
    public function optionsThings()
    {
    }

    #[OAT\Trace(
        path: '/things',
        operationId: 'traceThings',
        responses: [
            new OAT\Response(
                response: 200,
                description: 'Echo of the request',
                content: new OAT\MediaType(mediaType: 'message/http', schema: new OAT\Schema(type: 'string')),
            ),
        ],
    )]
    public function traceThings()
    {
    }
}
