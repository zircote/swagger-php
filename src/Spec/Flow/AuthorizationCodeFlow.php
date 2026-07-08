<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec\Flow;

use OpenApi\Spec;

#[\Attribute(\Attribute::IS_REPEATABLE)]
class AuthorizationCodeFlow extends Spec\Flow
{
    /**
     * @param array<string,string>|null $scopes
     * @param array<string,mixed>|null  $x
     */
    public function __construct(
        ?string $authorizationUrl = null,
        ?string $tokenUrl = null,
        ?string $refreshUrl = null,
        ?array $scopes = null,
        ?array $x = null,
    ) {
        parent::__construct(
            flow: 'authorizationCode',
            authorizationUrl: $authorizationUrl,
            tokenUrl: $tokenUrl,
            refreshUrl: $refreshUrl,
            scopes: $scopes,
            x: $x,
        );
    }
}
