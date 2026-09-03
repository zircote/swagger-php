<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace Fixtures\Scratch;

use OpenApi\Spec as OA;

#[OA\Info(title: 'Response', version: '1.0')]
class ResponseControllerSpec
{
    // Stacked siblings instead of constructor nesting, declared container-first:
    // the merge chain (Schema -> MediaType -> Response -> Post) resolves
    // inner-to-outer, so declaration order does not matter.
    #[OA\Operation\Post(
        path: '/endpoint/response-schema',
        operationId: 'responseSchema',
    )]
    #[OA\Response(response: 200, description: 'All good')]
    #[OA\MediaType(mediaType: 'application/octet-stream')]
    #[OA\Schema(type: 'string', format: 'byte')]
    public function responseSchema()
    {
    }
}
