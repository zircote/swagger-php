<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Generator;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Tag extends \OpenApi\Annotations\Tag
{
    public function __construct(
        string $name = Generator::UNDEFINED,
        string $description = Generator::UNDEFINED,
        ?ExternalDocumentation $externalDocs = null,
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
                'name' => $name,
                'description' => $description,
                'x' => $x ?? Generator::UNDEFINED,
                'value' => $this->combine($externalDocs, $attachables),
            ]);
    }
}
