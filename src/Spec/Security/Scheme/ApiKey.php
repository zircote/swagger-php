<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec\Security\Scheme;

use OpenApi\Spec as OA;

/**
 * An API key security scheme (header, query, or cookie).
 *
 * Allows to shorten this:
 *
 * ```php
 * #[OA\Security\Scheme(
 *     component: 'apiKey',
 *     type: 'apiKey',
 *     name: 'X-API-Key',
 *     in: 'header',
 * )]
 * ```
 *
 * to this:
 *
 * ```php
 * #[OA\Security\Scheme\ApiKey(
 *     component: 'apiKey',
 *     name: 'X-API-Key',
 *     in: 'header',
 * )]
 * ```
 *
 * @see [Security Scheme Object](https://spec.openapis.org/oas/v3.1.1.html#security-scheme-object)
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class ApiKey extends OA\Security\Scheme
{
    /**
     * @param array<string,mixed>|null $x
     * @param list<OA\Attachable>|null $attachables
     */
    public function __construct(
        ?string $securityScheme = null,
        ?string $description = null,
        ?string $name = null,
        string|OA\SchemeIn|null $in = null,
        ?string $component = null,
        ?array $x = null,
        ?array $attachables = null,
        ?OA\Fan $fan = null,
    ) {
        parent::__construct(
            securityScheme: $securityScheme,
            type: OA\SchemeType::ApiKey,
            description: $description,
            name: $name,
            in: $in,
            component: $component,
            x: $x,
            attachables: $attachables,
            fan: $fan,
        );
    }
}
