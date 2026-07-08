<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec\SecurityScheme;

use OpenApi\Spec;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class OpenIdConnectScheme extends Spec\SecurityScheme
{
    /**
     * @param array<string,mixed>|null $x
     */
    public function __construct(
        ?string $securityScheme = null,
        ?string $description = null,
        ?string $openIdConnectUrl = null,
        ?array $x = null,
    ) {
        parent::__construct(
            securityScheme: $securityScheme,
            type: 'openIdConnect',
            description: $description,
            openIdConnectUrl: $openIdConnectUrl,
            x: $x,
        );
    }
}
