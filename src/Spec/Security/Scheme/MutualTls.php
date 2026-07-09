<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec\Security\Scheme;

use OpenApi\Spec\Security\Scheme;

/**
 * A Mutual TLS security scheme.
 *
 * @see [Security Scheme Object](https://spec.openapis.org/oas/v3.1.1.html#security-scheme-object)
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class MutualTls extends Scheme
{
    /**
     * @param array<string,mixed>|null $x
     */
    public function __construct(
        ?string $securityScheme = null,
        ?string $description = null,
        ?array $x = null,
    ) {
        parent::__construct(
            securityScheme: $securityScheme,
            type: 'mutualTLS',
            description: $description,
            x: $x,
        );
    }
}
