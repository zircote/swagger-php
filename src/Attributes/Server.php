<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Generator;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Server extends \OpenApi\Annotations\Server
{
    public function __construct(
        ?string $url = null,
        ?string $description = null,
        ?array $variables = null,
        // annotation
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
                'url' => $url ?? Generator::UNDEFINED,
                'description' => $description ?? Generator::UNDEFINED,
                'x' => $x ?? Generator::UNDEFINED,
                'value' => $this->combine($variables, $attachables),
            ]);
    }
}
