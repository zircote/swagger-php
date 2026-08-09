<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace Fixtures\Scratch;

use OpenApi\Spec as OA;

#[OA\Info(title: 'Response', version: '1.0')]
class ResponseControllerSpec
{
    #[OA\Operation\Post(
        path: '/endpoint/response-schema',
        operationId: 'responseSchema',
        responses: [
            new OA\Response(
                response: 200,
                description: 'All good',
                content: new OA\MediaType(
                    mediaType: 'application/octet-stream',
                    schema: new OA\Schema(
                        type: 'string',
                        format: 'byte',
                    ),
                ),
            ),
        ]
    )]
    public function responseSchema()
    {
    }
}
