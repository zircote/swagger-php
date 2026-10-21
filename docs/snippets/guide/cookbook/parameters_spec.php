<?php

namespace Openapi\Snippets\Cookbook\Parameters;

use OpenApi\Spec as OA;

class PetController
{
    #[OA\Operation\Get(
        path: '/pets/{id}',
        parameters: [
            new OA\Parameter\Path(name: 'id', schema: new OA\Schema(type: 'integer')),
            new OA\Parameter\Query(name: 'fields', schema: new OA\Schema(type: 'string')),
            new OA\Parameter\Header(name: 'X-Request-Id', schema: new OA\Schema(type: 'string')),
        ],
    )]
    #[OA\Response(response: 200, description: 'The pet')]
    public function show()
    {
    }
}
