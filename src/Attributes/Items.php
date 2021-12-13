<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Generator;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class Items extends \OpenApi\Annotations\Items
{
    public function __construct(
        string $type = Generator::UNDEFINED,
        string $ref = Generator::UNDEFINED,
        ?bool $deprecated = null,
        ?array $allOf = null,
        ?array $anyOf = null,
        ?array $oneOf = null,
        ?bool $nullable = null,
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
                'type' => $type,
                'ref' => $ref,
                'nullable' => $nullable ?? Generator::UNDEFINED,
                'deprecated' => $deprecated ?? Generator::UNDEFINED,
                'allOf' => $allOf ?? Generator::UNDEFINED,
                'anyOf' => $anyOf ?? Generator::UNDEFINED,
                'oneOf' => $oneOf ?? Generator::UNDEFINED,
                'x' => $x ?? Generator::UNDEFINED,
                'value' => $this->combine($attachables),
            ]);
    }
}
