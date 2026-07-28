<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

enum SchemeType: string
{
    case ApiKey = 'apiKey';
    case Http = 'http';
    case MutualTLS = 'mutualTLS';
    case OAuth2 = 'oauth2';
    case OpenIdConnect = 'openIdConnect';
}
