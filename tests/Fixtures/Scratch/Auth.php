<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Fixtures\Scratch;

use OpenApi\Attributes as OAT;

/**
 * Where spec has a typed subclass per scheme type and per flow, classic carries the type as
 * a constructor argument.
 */
#[OAT\SecurityScheme(securityScheme: 'apiKeyHeader', type: 'apiKey', name: 'X-API-Key', in: 'header')]
#[OAT\SecurityScheme(securityScheme: 'apiKeyQuery', type: 'apiKey', name: 'api_key', in: 'query')]
#[OAT\SecurityScheme(securityScheme: 'apiKeyCookie', type: 'apiKey', name: 'SESSION', in: 'cookie')]
class AuthApiKeySchemes
{
}

#[OAT\SecurityScheme(securityScheme: 'basicAuth', type: 'http', scheme: 'basic')]
#[OAT\SecurityScheme(securityScheme: 'bearerAuth', type: 'http', bearerFormat: 'JWT', scheme: 'bearer')]
class AuthHttpSchemes
{
}

#[OAT\SecurityScheme(
    securityScheme: 'oauth2',
    type: 'oauth2',
    description: 'All four flows on a single scheme.',
    flows: [
        new OAT\Flow(
            authorizationUrl: 'https://example.com/oauth/authorize',
            flow: 'implicit',
            scopes: ['read:pets' => 'Read pets'],
        ),
        new OAT\Flow(
            tokenUrl: 'https://example.com/oauth/token',
            flow: 'password',
            scopes: ['read:pets' => 'Read pets'],
        ),
        new OAT\Flow(
            tokenUrl: 'https://example.com/oauth/token',
            refreshUrl: 'https://example.com/oauth/refresh',
            flow: 'clientCredentials',
            scopes: ['write:pets' => 'Write pets'],
        ),
        new OAT\Flow(
            authorizationUrl: 'https://example.com/oauth/authorize',
            tokenUrl: 'https://example.com/oauth/token',
            refreshUrl: 'https://example.com/oauth/refresh',
            flow: 'authorizationCode',
            scopes: ['read:pets' => 'Read pets', 'write:pets' => 'Write pets'],
        ),
    ],
)]
class AuthOAuth2Scheme
{
}

#[OAT\SecurityScheme(
    securityScheme: 'openIdConnect',
    type: 'openIdConnect',
    openIdConnectUrl: 'https://example.com/.well-known/openid-configuration',
)]
class AuthOtherSchemes
{
}

// `mutualTLS` has no classic counterpart: OAT\SecurityScheme validates `type` against
// http / apiKey / oauth2 / openIdConnect only, so it is spec-only. See Auth-spec.php.

#[OAT\Info(title: 'Auth', version: '1.0')]
#[OAT\Get(
    path: '/secured',
    operationId: 'getSecured',
    security: [
        ['bearerAuth' => []],
        ['oauth2' => ['read:pets']],
    ],
    responses: [
        new OAT\Response(response: 200, description: 'All good'),
    ],
)]
class AuthEndpoint
{
}
