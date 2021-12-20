<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Generator;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Flow extends \OpenApi\Annotations\Flow
{
    public function __construct(
        string $authorizationUrl = Generator::UNDEFINED,
        string $tokenUrl = Generator::UNDEFINED,
        string $refreshUrl = Generator::UNDEFINED,
        string $flow = Generator::UNDEFINED,
        ?array $scopes = null,
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
                'authorizationUrl' => $authorizationUrl,
                'tokenUrl' => $tokenUrl,
                'refreshUrl' => $refreshUrl,
                'flow' => $flow,
                'scopes' => $scopes ?? Generator::UNDEFINED,
                'x' => $x ?? Generator::UNDEFINED,
                'value' => $this->combine($attachables),
            ]);
    }
}
