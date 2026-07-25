<?php

namespace Openapi\Snippets\Cookbook\SecuritySchemas;

use OpenApi\Spec as OA;

#[OA\Security\Scheme\ApiKey(
    name: 'api_key',
    securityScheme: 'api_key',
    in: 'header',
)]
#[OA\Security\Scheme\OAuth2(
    securityScheme: 'petstore_auth',
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
