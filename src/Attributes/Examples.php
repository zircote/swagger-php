<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Generator;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Examples extends \OpenApi\Annotations\Examples
{
    /**
     * @param array<string,string>|null $x
     * @param Attachable[]|null         $attachables
     */
    public function __construct(
        ?string $summary = null,
        ?string $description = null,
        ?string $value = null,
        ?string $externalValue = null,
        string|object|null $ref = null,
        // annotation
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
            'summary' => $summary ?? Generator::UNDEFINED,
            'description' => $description ?? Generator::UNDEFINED,
            'value' => $value ?? Generator::UNDEFINED,
            'externalValue' => $externalValue ?? Generator::UNDEFINED,
            'ref' => $ref ?? Generator::UNDEFINED,
            'x' => $x ?? Generator::UNDEFINED,
        ]);
        if ($attachables) {
            $this->merge($attachables);
        }
    }
}
