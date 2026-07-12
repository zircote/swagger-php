<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec\Security;

use OpenApi\Spec as OA;

/**
 * Defines a security scheme that can be used by the operations.
 *
 * Typed subtypes are available for each security scheme type:
 * - `OA\Security\Scheme\Http` - HTTP authentication (Basic, Bearer, etc.)
 * - `OA\Security\Scheme\ApiKey` - API key in header, query, or cookie
 * - `OA\Security\Scheme\OAuth2` - OAuth2 with one or more flows
 * - `OA\Security\Scheme\OpenIdConnect` - OpenID Connect discovery
 * - `OA\Security\Scheme\MutualTls` - Mutual TLS authentication
 *
 * @see [Security Scheme Object](https://spec.openapis.org/oas/v3.1.1.html#security-scheme-object)
 */
class Scheme extends OA\AbstractAttribute
{
    /**
     * @param string|null              $securityScheme   Reusable security scheme identifier (component key)
     * @param string|null              $type             The type of the security scheme (apiKey, http, mutualTLS, oauth2, openIdConnect)
     * @param string|null              $description      A description of the security scheme (CommonMark syntax)
     * @param string|null              $name             The name of the header, query, or cookie parameter (apiKey)
     * @param string|null              $in               The location of the API key (query, header, cookie)
     * @param string|null              $scheme           The HTTP authorization scheme (http)
     * @param string|null              $bearerFormat     A hint about the format of the bearer token (http/bearer)
     * @param string|null              $openIdConnectUrl The OpenID Connect URL to discover configuration (openIdConnect)
     * @param list<OA\Flow>|null       $flows            The available OAuth2 flows (oauth2)
     * @param string|null              $ref              A JSON Reference to a reusable security scheme
     * @param array<string,mixed>|null $x                Vendor extensions (x-* properties)
     */
    public function __construct(
        public ?string $securityScheme = null,
        public ?string $type = null,
        public ?string $description = null,
        public ?string $name = null,
        public ?string $in = null,
        public ?string $scheme = null,
        public ?string $bearerFormat = null,
        public ?string $openIdConnectUrl = null,
        public ?array $flows = null,
        public ?string $ref = null,
        ?array $x = null,
    ) {
        parent::__construct(x: $x);
    }

    public function isRoot(): bool
    {
        return true;
    }

    public function contains(): array
    {
        return [OA\Flow::class => 'flows[]'];
    }
}
