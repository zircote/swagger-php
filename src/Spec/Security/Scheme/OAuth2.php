<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec\Security\Scheme;

use OpenApi\Spec as OA;

/**
 * An OAuth2 security scheme with one or more flows.
 *
 * @see [Security Scheme Object](https://spec.openapis.org/oas/v3.1.1.html#security-scheme-object)
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class OAuth2 extends OA\Security\Scheme
{
    /**
     * @param list<OA\Flow>|null       $flows
     * @param array<string,mixed>|null $x
     * @param list<OA\Attachable>|null $attachables
     */
    public function __construct(
        ?string $securityScheme = null,
        ?string $description = null,
        ?bool $deprecated = null,
        ?array $flows = null,
        ?string $oauth2MetadataUrl = null,
        ?array $x = null,
        ?array $attachables = null,
    ) {
        parent::__construct(
            securityScheme: $securityScheme,
            type: OA\SchemeType::OAuth2,
            description: $description,
            deprecated: $deprecated,
            flows: $flows,
            oauth2MetadataUrl: $oauth2MetadataUrl,
            x: $x,
            attachables: $attachables,
        );
    }
}
