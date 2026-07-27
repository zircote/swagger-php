<?php

namespace Openapi\Snippets\Cookbook\SecuritySchemaTips;

use OpenApi\Spec as OA;

class Controller
{
    #[OA\Operation\Get(
        path: '/api/secure/',
        summary: 'Requires authentication',
        security: [
            new OA\Security\Requirement(scheme: 'api_key', scopes: []),
            ['petstore_auth' => ['write:pets', 'read:pets']],
        ]
    )]
    public function secure()
    {
    }
}
