<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Generator;

#[\Attribute(\Attribute::TARGET_CLASS)]
class ExternalDocumentation extends \OpenApi\Annotations\ExternalDocumentation
{
    public function __construct(
        string $description = Generator::UNDEFINED,
        string $url = Generator::UNDEFINED,
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
                'description' => $description,
                'url' => $url,
                'x' => $x ?? Generator::UNDEFINED,
                'value' => $this->combine($attachables),
            ]);
    }
}
