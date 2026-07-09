<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

/**
 * Configuration details for a supported OAuth2 flow.
 *
 * @see [OAuth Flow Object](https://spec.openapis.org/oas/v3.1.1.html#oauth-flow-object)
 * @see [OAuth Flows Object](https://spec.openapis.org/oas/v3.1.1.html#oauth-flows-object)
 */
#[\Attribute(\Attribute::IS_REPEATABLE)]
class Flow extends AbstractAttribute
{
    /**
     * @param string|null               $flow             The OAuth2 flow type (implicit, password, clientCredentials, authorizationCode)
     * @param string|null               $authorizationUrl The authorization URL for this flow
     * @param string|null               $tokenUrl         The token URL for this flow
     * @param string|null               $refreshUrl       The URL for obtaining refresh tokens
     * @param array<string,string>|null $scopes           The available scopes for the OAuth2 security scheme
     * @param array<string,mixed>|null  $x                Vendor extensions (x-* properties)
     */
    public function __construct(
        public ?string $flow = null,
        public ?string $authorizationUrl = null,
        public ?string $tokenUrl = null,
        public ?string $refreshUrl = null,
        public ?array $scopes = null,
        ?array $x = null,
    ) {
        parent::__construct(x: $x);
    }

    public function merge(): array
    {
        return [SecurityScheme::class => 'flows[]'];
    }
}
