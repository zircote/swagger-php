<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

/**
 * Configuration details for a supported OAuth2 flow.
 *
 * Typed subtypes pre-fill the flow type:
 * - `OA\Flow\Implicit` - implicit grant (authorizationUrl required)
 * - `OA\Flow\Password` - resource owner password credentials (tokenUrl required)
 * - `OA\Flow\ClientCredentials` - client credentials grant (tokenUrl required)
 * - `OA\Flow\AuthorizationCode` - authorization code grant (authorizationUrl + tokenUrl required)
 *
 *   #[OA\Security\Scheme\OAuth2(securityScheme: 'oauth2', flows: [
 *       new OA\Flow\AuthorizationCode(
 *           authorizationUrl: 'https://example.com/oauth/authorize',
 *           tokenUrl: 'https://example.com/oauth/token',
 *           scopes: ['read:pets' => 'Read pets', 'write:pets' => 'Write pets'],
 *       ),
 *   ])]
 *
 * Produces:
 *   components:
 *     securitySchemes:
 *       oauth2:
 *         type: oauth2
 *         flows:
 *           authorizationCode:
 *             authorizationUrl: https://example.com/oauth/authorize
 *             tokenUrl: https://example.com/oauth/token
 *             scopes:
 *               read:pets: Read pets
 *               write:pets: Write pets
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
        return [Security\Scheme::class => 'flows[]'];
    }
}
