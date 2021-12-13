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
        string $url = Generator::UNDEFINED,
        string $description = Generator::UNDEFINED,
        ?array $variables = null,
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
                'url' => $url,
                'description' => $description,
                'x' => $x ?? Generator::UNDEFINED,
                'value' => $this->combine($variables, $attachables),
            ]);
    }
}
