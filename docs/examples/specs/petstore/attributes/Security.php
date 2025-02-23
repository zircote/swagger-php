<?php

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Petstore\Attributes;

use OpenApi\Attributes as OAT;

#[OAT\SecurityScheme(
    type: 'oauth2',
    name: 'petstore_auth',
    securityScheme: 'petstore_auth',
    flows: [
        new OAT\Flow(
            flow: 'implicit',
            authorizationUrl: 'http://petstore.swagger.io/oauth/dialog',
            scopes: [
                'write:pets' => 'modify pets in your account',
                'read:pets' => 'read your pets',
            ]
        ),
    ]
)]
#[OAT\SecurityScheme(
    type: 'apiKey',
    name: 'api_key',
    in: 'header',
    securityScheme: 'api_key',
)]
class Security
{
}
