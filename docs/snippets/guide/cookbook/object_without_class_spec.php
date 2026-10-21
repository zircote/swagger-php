<?php

namespace Openapi\Snippets\Cookbook\ObjectWithoutClass;

use OpenApi\Spec as OA;

class UserController
{
    #[OA\Operation\Get(path: '/users/me')]
    #[OA\Response(
        response: 200,
        description: 'The signed in user',
        content: [new OA\MediaType\Json(properties: [
            'name' => OA\Type::String,
            'email' => OA\Format::Email,
            'lastLogin' => OA\Format::DateTime,
        ])],
    )]
    public function me()
    {
    }
}
