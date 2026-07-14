<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec\Flow;

use OpenApi\Spec as OA;

/**
 * Configuration for the OAuth2 Implicit flow.
 *
 * @see [OAuth Flow Object](https://spec.openapis.org/oas/v3.1.1.html#oauth-flow-object)
 */
#[\Attribute(\Attribute::IS_REPEATABLE)]
class Implicit extends OA\Flow
{
    /**
     * @param array<string,string>|null $scopes
     * @param array<string,mixed>|null  $x
     * @param list<OA\Attachable>|null  $attachables
     */
    public function __construct(
        ?string $authorizationUrl = null,
        ?string $refreshUrl = null,
        ?array $scopes = null,
        ?array $x = null,
        ?array $attachables = null,
    ) {
        parent::__construct(
            flow: 'implicit',
            authorizationUrl: $authorizationUrl,
            refreshUrl: $refreshUrl,
            scopes: $scopes,
            x: $x,
            attachables: $attachables,
        );
    }
}
