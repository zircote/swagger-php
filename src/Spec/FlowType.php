<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

enum FlowType: string
{
    case Implicit = 'implicit';
    case Password = 'password';
    case ClientCredentials = 'clientCredentials';
    case AuthorizationCode = 'authorizationCode';
}
