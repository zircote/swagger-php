<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Spec as OA;

/**
 * Every security scheme type, and every OAuth2 flow.
 */
#[OA\Security\Scheme\ApiKey(securityScheme: 'apiKeyHeader', name: 'X-API-Key', in: 'header')]
#[OA\Security\Scheme\ApiKey(securityScheme: 'apiKeyQuery', name: 'api_key', in: 'query')]
#[OA\Security\Scheme\ApiKey(securityScheme: 'apiKeyCookie', name: 'SESSION', in: 'cookie')]
class AuthApiKeySchemesSpec
{
}

#[OA\Security\Scheme\Http(securityScheme: 'basicAuth', scheme: 'basic')]
#[OA\Security\Scheme\Http(securityScheme: 'bearerAuth', scheme: 'bearer', bearerFormat: 'JWT')]
class AuthHttpSchemesSpec
{
}

#[OA\Security\Scheme\OAuth2(
    securityScheme: 'oauth2',
    description: 'All four flows on a single scheme.',
    flows: [
        new OA\Flow\Implicit(
            authorizationUrl: 'https://example.com/oauth/authorize',
            scopes: ['read:pets' => 'Read pets'],
        ),
        new OA\Flow\Password(
            tokenUrl: 'https://example.com/oauth/token',
            scopes: ['read:pets' => 'Read pets'],
        ),
        new OA\Flow\ClientCredentials(
            tokenUrl: 'https://example.com/oauth/token',
            refreshUrl: 'https://example.com/oauth/refresh',
            scopes: ['write:pets' => 'Write pets'],
        ),
        new OA\Flow\AuthorizationCode(
            authorizationUrl: 'https://example.com/oauth/authorize',
            tokenUrl: 'https://example.com/oauth/token',
            refreshUrl: 'https://example.com/oauth/refresh',
            scopes: ['read:pets' => 'Read pets', 'write:pets' => 'Write pets'],
        ),
    ],
)]
class AuthOAuth2SchemeSpec
{
}

#[OA\Security\Scheme\OpenIdConnect(
    securityScheme: 'openIdConnect',
    openIdConnectUrl: 'https://example.com/.well-known/openid-configuration',
)]
#[OA\Security\Scheme\MutualTls(securityScheme: 'mutualTls', description: 'Client certificate.')]
class AuthOtherSchemesSpec
{
}

#[OA\Info(title: 'Auth', version: '1.0')]
#[OA\Operation\Get(
    path: '/secured',
    operationId: 'getSecured',
    security: [
        new OA\Security\Requirement(scheme: 'bearerAuth'),
        new OA\Security\Requirement(scheme: 'oauth2', scopes: ['read:pets']),
    ],
)]
#[OA\Response(response: 200, description: 'All good')]
class AuthEndpointSpec
{
}
