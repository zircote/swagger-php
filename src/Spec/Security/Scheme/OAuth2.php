<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec\Security\Scheme;

use OpenApi\Spec\Flow;
use OpenApi\Spec\Security\Scheme;

/**
 * An OAuth2 security scheme with one or more flows.
 *
 * @see [Security Scheme Object](https://spec.openapis.org/oas/v3.1.1.html#security-scheme-object)
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class OAuth2 extends Scheme
{
    /**
     * @param list<Flow>|null          $flows
     * @param array<string,mixed>|null $x
     */
    public function __construct(
        ?string $securityScheme = null,
        ?string $description = null,
        ?array $flows = null,
        ?array $x = null,
    ) {
        parent::__construct(
            securityScheme: $securityScheme,
            type: 'oauth2',
            description: $description,
            flows: $flows,
            x: $x,
        );
    }
}
