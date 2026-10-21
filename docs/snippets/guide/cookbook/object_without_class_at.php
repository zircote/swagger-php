<?php

namespace Openapi\Snippets\Cookbook\ObjectWithoutClass;

use OpenApi\Attributes as OA;

class UserController
{
    #[OA\Get(path: '/users/me')]
    #[OA\Response(
        response: 200,
        description: 'The signed in user',
        content: new OA\JsonContent(properties: [
            new OA\Property(property: 'name', type: 'string'),
            new OA\Property(property: 'email', type: 'string', format: 'email'),
            new OA\Property(property: 'lastLogin', type: 'string', format: 'date-time'),
        ]),
    )]
    public function me()
    {
    }
}
