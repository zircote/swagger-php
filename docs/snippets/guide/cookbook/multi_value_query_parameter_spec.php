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
            new OA\Parameter\Query(
                name: 'things[]',
                description: 'A list of things.',
                required: false,
                schema: new OA\Schema\Items(type: OA\Type::Integer),
            ),
        ],
    )]
    #[OA\Response(response: '200', description: 'All good')]
    public function endpoint()
    {
        // ...
    }
}
