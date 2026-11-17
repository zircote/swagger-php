<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec\Flow;

use OpenApi\Spec as OA;

/**
 * Configuration for the OAuth2 Device Authorization flow, from OpenAPI 3.2 on.
 *
 * Allows to shorten this:
 *
 * ```php
 * new OA\Flow(
 *     flow: 'deviceAuthorization',
 *     deviceAuthorizationUrl: 'https://example.com/oauth/device',
 *     tokenUrl: 'https://example.com/oauth/token',
 *     scopes: ['read:pets' => 'Read your pets'],
 * )
 * ```
 *
 * to this:
 *
 * ```php
 * new OA\Flow\DeviceAuthorization(
 *     deviceAuthorizationUrl: 'https://example.com/oauth/device',
 *     tokenUrl: 'https://example.com/oauth/token',
 *     scopes: ['read:pets' => 'Read your pets'],
 * )
 * ```
 *
 * @see [OAuth Flow Object](https://spec.openapis.org/oas/v3.2.0.html#oauth-flow-object)
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class DeviceAuthorization extends OA\Flow
{
    /**
     * @param array<string,string>|null $scopes
     * @param array<string,mixed>|null  $x
     * @param list<OA\Attachable>|null  $attachables
     */
    public function __construct(
        ?string $deviceAuthorizationUrl = null,
        ?string $tokenUrl = null,
        ?string $refreshUrl = null,
        ?array $scopes = null,
        ?array $x = null,
        ?array $attachables = null,
        ?OA\Fan $fan = null,
    ) {
        parent::__construct(
            flow: OA\FlowType::DeviceAuthorization,
            tokenUrl: $tokenUrl,
            refreshUrl: $refreshUrl,
            scopes: $scopes,
            x: $x,
            attachables: $attachables,
            fan: $fan,
            deviceAuthorizationUrl: $deviceAuthorizationUrl,
        );
    }
}
