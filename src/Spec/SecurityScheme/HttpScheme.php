<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec\SecurityScheme;

use OpenApi\Spec;

/**
 * An HTTP authentication security scheme (Basic, Bearer, etc.).
 *
 * @see [Security Scheme Object](https://spec.openapis.org/oas/v3.1.1.html#security-scheme-object)
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class HttpScheme extends Spec\SecurityScheme
{
    /**
     * @param array<string,mixed>|null $x
     */
    public function __construct(
        ?string $securityScheme = null,
        ?string $description = null,
        ?string $scheme = null,
        ?string $bearerFormat = null,
        ?array $x = null,
    ) {
        parent::__construct(
            securityScheme: $securityScheme,
            type: 'http',
            description: $description,
            scheme: $scheme,
            bearerFormat: $bearerFormat,
            x: $x,
        );
    }
}
