<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Generator;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Examples extends \OpenApi\Annotations\Examples
{
    public function __construct(
        string $summary = Generator::UNDEFINED,
        string $description = Generator::UNDEFINED,
        string $value = Generator::UNDEFINED,
        string $externalValue = Generator::UNDEFINED,
        string $ref = Generator::UNDEFINED,
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
                'summary' => $summary,
                'description' => $description,
                'value' => $value,
                'externalValue' => $externalValue,
                'ref' => $ref,
                'x' => $x ?? Generator::UNDEFINED,
            ]);
        if ($attachables) {
            $this->merge($attachables);
        }
    }
}
