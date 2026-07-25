<?php

namespace Openapi\Snippets\Cookbook\MultiValueQueryParameter;

use OpenApi\Spec as OA;

class Controller
{
    #[OA\Operation\Get(
        path: '/api/endpoint',
        description: 'The endpoint',
        operationId: 'endpoint',
        tags: ['endpoints'],
        parameters: [
            new OA\Parameter(
                name: 'things[]',
                in: 'query',
                description: 'A list of things.',
                required: false,
                schema: new OA\Schema(
                    type: 'array',
                    items: new OA\Schema(type: 'integer')
                )
            ),
        ],
        responses: [
            new OA\Response(response: '200', description: 'All good'),
        ]
    )]
    public function endpoint()
    {
        // ...
    }
}
