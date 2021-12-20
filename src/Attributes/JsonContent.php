<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Generator;

#[\Attribute(\Attribute::TARGET_CLASS)]
class JsonContent extends \OpenApi\Annotations\JsonContent
{
    public function __construct(
        string $ref = Generator::UNDEFINED,
        ?array $allOf = null,
        ?array $anyOf = null,
        ?array $oneOf = null,
        string $type = Generator::UNDEFINED,
        $items = Generator::UNDEFINED,
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
                'ref' => $ref,
                'allOf' => $allOf ?? Generator::UNDEFINED,
                'anyOf' => $anyOf ?? Generator::UNDEFINED,
                'oneOf' => $oneOf ?? Generator::UNDEFINED,
                'type' => $type,
                'x' => $x ?? Generator::UNDEFINED,
                'value' => $this->combine($items, $attachables),
            ]);
    }
}
