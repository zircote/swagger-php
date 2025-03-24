<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

#[OAT\SecurityScheme(
    type: 'apiKey',
    name: 'api_key',
    in: 'header',
    securityScheme: 'api_key',
)]
#[OAT\SecurityScheme(
    type: 'oauth2',
    securityScheme: 'store_auth',
    flows: [
        new OAT\Flow(
            authorizationUrl: 'http://store.local/oauth/dialog',
            flow: 'implicit',
            scopes: [],
        ),
        new OAT\Flow(
            authorizationUrl: 'http://store.local/login',
            flow: 'password',
            scopes: [
                'read:products' => 'Access products',
            ],
        ),
    ],
)]
class Security
{
}

#[OAT\Info(title: 'Security', version: '1.0')]
#[OAT\Get(
    path: '/endpoint',
    responses: [
        new OAT\Response(
            response: 200,
            description: 'All good',
        ),
    ]
)]
class SecurityEndpoint
{
}
