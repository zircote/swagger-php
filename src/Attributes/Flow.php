<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Annotations as OA;
use OpenApi\Undefined;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Flow extends OA\Flow
{
    /**
     * @param 'implicit'|'password'|'authorizationCode'|'clientCredentials'|null $flow
     * @param array<string,mixed>|null                                           $x
     * @param list<Attachable>|null                                              $attachables
     */
    public function __construct(
        ?string $authorizationUrl = null,
        ?string $tokenUrl = null,
        ?string $refreshUrl = null,
        ?string $flow = null,
        ?array $scopes = null,

        // abstract annotation
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
                'authorizationUrl' => $authorizationUrl ?? Undefined::UNDEFINED,
                'tokenUrl' => $tokenUrl ?? Undefined::UNDEFINED,
                'refreshUrl' => $refreshUrl ?? Undefined::UNDEFINED,
                'flow' => $flow ?? Undefined::UNDEFINED,
                'scopes' => $scopes ?? Undefined::UNDEFINED,
                'x' => $x ?? Undefined::UNDEFINED,
                'attachables' => $attachables ?? Undefined::UNDEFINED,
            ]);
    }
}
