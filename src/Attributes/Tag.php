<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Generator;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Tag extends \OpenApi\Annotations\Tag
{
    /**
     * @param array<string,string>|null $x
     * @param Attachable[]|null         $attachables
     */
    public function __construct(
        ?string $name = null,
        ?string $description = null,
        ?ExternalDocumentation $externalDocs = null,
        // annotation
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
                'name' => $name ?? Generator::UNDEFINED,
                'description' => $description ?? Generator::UNDEFINED,
                'x' => $x ?? Generator::UNDEFINED,
                'value' => $this->combine($externalDocs, $attachables),
            ]);
    }
}
