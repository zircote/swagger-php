<?php

namespace Openapi\Snippets\Cookbook\Parameters;

use OpenApi\Attributes as OA;

class PetController
{
    #[OA\Get(
        path: '/pets/{id}',
        parameters: [
            new OA\PathParameter(name: 'id', schema: new OA\Schema(type: 'integer')),
            new OA\QueryParameter(name: 'fields', schema: new OA\Schema(type: 'string')),
            new OA\HeaderParameter(name: 'X-Request-Id', schema: new OA\Schema(type: 'string')),
        ],
    )]
    #[OA\Response(response: 200, description: 'The pet')]
    public function show()
    {
    }
}
