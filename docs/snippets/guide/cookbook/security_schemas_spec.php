<?php

namespace Openapi\Snippets\Cookbook\SecuritySchemas;

use OpenApi\Spec as OA;

#[OA\Security\Scheme\ApiKey(
    name: 'api_key',
    component: 'api_key',
    in: OA\SchemeIn::Header,
)]
#[OA\Security\Scheme\OAuth2(
    component: 'petstore_auth',
    flows: [
        new OA\Flow(
            flow: 'implicit',
            authorizationUrl: 'http://petstore.swagger.io/oauth/dialog',
            scopes: [
                'read:pets' => 'read your pets',
                'write:pets' => 'modify pets in your account',
            ],
        ),
    ],
)]
class OpenApiSpec
{
}
