<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

#[OA\Security\Scheme\ApiKey(
    name: 'api_key',
    in: 'header',
    securityScheme: 'api_key',
)]
#[OA\Security\Scheme\OAuth2(
    securityScheme: 'store_auth',
    flows: [
        new OA\Flow(
            authorizationUrl: 'http://store.local/oauth/dialog',
            flow: OA\FlowType::Implicit,
        ),
        new OA\Flow(
            authorizationUrl: 'http://store.local/login',
            flow: 'password',
            scopes: [
                'read:products' => 'Access products',
            ],
        ),
    ],
)]
class SecuritySpec
{
}

#[OA\Info(title: 'Security', version: '1.0')]
#[OA\Operation\Get(
    path: '/endpoint',
    operationId: 'getInheritedFilters',
    responses: [
        new OA\Response(
            response: 200,
            description: 'All good',
        ),
    ]
)]
class SecurityEndpointSpec
{
}
