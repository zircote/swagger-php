<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

#[OAT\Info(title: 'Response', version: '1.0')]
class ResponseController
{
    #[OAT\Post(
        path: '/endpoint/response-schema',
        responses: [
            new OAT\Response(
                response: 200,
                description: 'All good',
                content: new OAT\MediaType(
                    mediaType: 'application/octet-stream',
                    schema: new OAT\Schema(
                        type: 'string',
                        format: 'byte',
                    ),
                ),            ),
        ]
    )]
    public function responseSchema()
    {
    }
}
