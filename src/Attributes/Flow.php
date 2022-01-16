<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Generator;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Flow extends \OpenApi\Annotations\Flow
{
    /**
     * @param array<string,string>|null $x
     * @param Attachable[]|null         $attachables
     */
    public function __construct(
        ?string $authorizationUrl = null,
        ?string $tokenUrl = null,
        ?string $refreshUrl = null,
        ?string $flow = null,
        ?array $scopes = null,
        // annotation
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
                'authorizationUrl' => $authorizationUrl ?? Generator::UNDEFINED,
                'tokenUrl' => $tokenUrl ?? Generator::UNDEFINED,
                'refreshUrl' => $refreshUrl ?? Generator::UNDEFINED,
                'flow' => $flow ?? Generator::UNDEFINED,
                'scopes' => $scopes ?? Generator::UNDEFINED,
                'x' => $x ?? Generator::UNDEFINED,
                'value' => $this->combine($attachables),
            ]);
    }
}
