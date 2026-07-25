<?php

namespace Openapi\Snippets\Cookbook\SecureEndpoint;

use OpenApi\Spec as OA;

class Controller
{
    #[OA\Operation\Get(
        path: '/api/secure/',
        summary: 'Requires authentication',
        security: [
            new OA\Security\Requirement(scheme: 'api_key', scopes: []),
        ],
    )]
    public function getSecurely()
    {
        // ...
    }
}
