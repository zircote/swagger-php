<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Generator;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY)]
class Schema extends \OpenApi\Annotations\Schema
{
    public function __construct(
        string $schema = Generator::UNDEFINED,
        string $description = Generator::UNDEFINED,
        string $title = Generator::UNDEFINED,
        string $type = Generator::UNDEFINED,
        string $format = Generator::UNDEFINED,
        string $ref = Generator::UNDEFINED,
        ?Items $items = null,
        ?array $enum = null,
        ?bool $deprecated = null,
        ?ExternalDocumentation $externalDocs = null,
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
                'schema' => $schema,
                'description' => $description,
                'title' => $title,
                'type' => $type,
                'format' => $format,
                'ref' => $ref,
                'enum' => $enum ?? Generator::UNDEFINED,
                'deprecated' => $deprecated ?? Generator::UNDEFINED,
                'x' => $x ?? Generator::UNDEFINED,
                'value' => $this->combine($externalDocs, $items, $attachables),
            ]);
    }
}
