<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec\SecurityScheme;

use OpenApi\Spec;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class OAuth2Scheme extends Spec\SecurityScheme
{
    /**
     * @param list<Spec\Flow>|null     $flows
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
