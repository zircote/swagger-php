<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Generator;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY)]
class Property extends \OpenApi\Annotations\Property
{
    public function __construct(
        string $property = Generator::UNDEFINED,
        string $description = Generator::UNDEFINED,
        string $title = Generator::UNDEFINED,
        string $type = Generator::UNDEFINED,
        string $format = Generator::UNDEFINED,
        string $ref = Generator::UNDEFINED,
        ?array $allOf = null,
        ?array $anyOf = null,
        ?array $oneOf = null,
        ?bool $nullable = null,
        ?Items $items = null,
        ?bool $deprecated = null,
        $example = Generator::UNDEFINED,
        $examples = Generator::UNDEFINED,
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
                'property' => $property,
                'description' => $description,
                'title' => $title,
                'type' => $type,
                'format' => $format,
                'nullable' => $nullable ?? Generator::UNDEFINED,
                'deprecated' => $deprecated ?? Generator::UNDEFINED,
                'example' => $example,
                'ref' => $ref,
                'allOf' => $allOf ?? Generator::UNDEFINED,
                'anyOf' => $anyOf ?? Generator::UNDEFINED,
                'oneOf' => $oneOf ?? Generator::UNDEFINED,
                'x' => $x ?? Generator::UNDEFINED,
                'value' => $this->combine($items, $examples, $attachables),
            ]);
    }
}
