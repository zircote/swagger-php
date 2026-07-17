<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Examples\Specs\Petstore\Spec;

use OpenApi\Spec as OA;

#[OA\Security\Scheme\OAuth2(securityScheme: 'petstore_auth', flows: [
    new OA\Flow\Implicit(
        authorizationUrl: 'http://petstore.swagger.io/oauth/dialog',
        scopes: [
            'write:pets' => 'modify pets in your account',
            'read:pets' => 'read your pets',
        ],
    ),
])]
#[OA\Security\Scheme\ApiKey(securityScheme: 'api_key', name: 'api_key', in: 'header')]
class Security
{
}
