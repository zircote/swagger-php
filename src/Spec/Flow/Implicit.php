<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec\Flow;

use OpenApi\Spec as OA;

/**
 * Configuration for the OAuth2 Implicit flow.
 *
 * Allows to shorten this:
 *
 * ```php
 * new OA\Flow(
 *     flow: 'implicit',
 *     authorizationUrl: 'https://example.com/oauth/authorize',
 *     scopes: ['read:pets' => 'Read your pets'],
 * )
 * ```
 *
 * to this:
 *
 * ```php
 * new OA\Flow\Implicit(
 *     authorizationUrl: 'https://example.com/oauth/authorize',
 *     scopes: ['read:pets' => 'Read your pets'],
 * )
 * ```
 *
 * @see [OAuth Flow Object](https://spec.openapis.org/oas/v3.1.1.html#oauth-flow-object)
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
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
        ?OA\Fan $fan = null,
    ) {
        parent::__construct(
            flow: OA\FlowType::Implicit,
            authorizationUrl: $authorizationUrl,
            refreshUrl: $refreshUrl,
            scopes: $scopes,
            x: $x,
            attachables: $attachables,
            fan: $fan,
        );
    }
}
