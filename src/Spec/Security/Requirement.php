<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec\Security;

use OpenApi\Spec as OA;

/**
 * A security requirement declaring which security schemes apply.
 *
 * Each requirement instance represents one entry in the security array (OR logic).
 * Multiple schemes within a single requirement represent AND logic.
 *
 * @see [Security Requirement Object](https://spec.openapis.org/oas/v3.1.1.html#security-requirement-object)
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Requirement extends OA\AbstractAttribute
{
    /**
     * @param string|null                     $scheme  Single scheme name (shorthand for simple requirements)
     * @param list<string>|null               $scopes  Scopes for the single scheme (OAuth2/OpenIdConnect)
     * @param array<string,list<string>>|null $schemes Map of scheme names to scopes (for AND logic with multiple schemes)
     * @param array<string,mixed>|null        $x       Vendor extensions (x-* properties)
     */
    public function __construct(
        public ?string $scheme = null,
        public ?array $scopes = null,
        public ?array $schemes = null,
        ?array $x = null,
    ) {
        parent::__construct(x: $x);
    }

    public function merge(): array
    {
        return [OA\OpenApi::class => 'security[]', OA\Operation::class => 'security[]'];
    }

    /**
     * Resolve to the normalized map format: ['schemeName' => [...scopes]].
     *
     * @return array<string, list<string>>
     */
    public function toArray(): array
    {
        if ($this->schemes !== null) {
            return $this->schemes;
        }

        if ($this->scheme !== null) {
            return [$this->scheme => $this->scopes ?? []];
        }

        return [];
    }
}
