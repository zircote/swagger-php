<?php

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Petstore\Attributes;

use OpenApi\Attributes as OAT;

#[OAT\SecurityScheme(securityScheme: 'petstore_auth', type: 'oauth2', name: 'petstore_auth', flows: [
    new OAT\Flow(
        authorizationUrl: 'http://petstore.swagger.io/oauth/dialog',
        flow: 'implicit',
        scopes: [
            'write:pets' => 'modify pets in your account',
            'read:pets' => 'read your pets',
        ]
    ),
])]
#[OAT\SecurityScheme(securityScheme: 'api_key', type: 'apiKey', name: 'api_key', in: 'header')]
class Security
{
}
