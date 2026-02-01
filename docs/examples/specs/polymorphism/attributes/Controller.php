<?php

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Polymorphism\Attributes;

use OpenApi\Attributes as OAT;

#[OAT\Info(version: '1', description: 'Polymorphism example', title: 'Polymorphism', contact: new OAT\Contact(
    name: 'Swagger API Team'
))]
#[OAT\Tag(
    name: 'api',
    description: 'API operations'
)]
#[OAT\Server(
    url: 'https://example.localhost',
    description: 'API server'
)]
class Controller
{
    #[OAT\Get(
        path: '/test',
        operationId: 'test',
        description: 'Get test',
        tags: ['api'],
        responses: [
            new OAT\Response(
                response: 200,
                description: 'Polymorphism',
                content: new OAT\JsonContent(
                    ref: Request::class
                )
            ),
        ]
    )]
    public function getProduct($id)
    {
    }
}
