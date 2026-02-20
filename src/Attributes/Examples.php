<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Annotations as OA;
use OpenApi\Generator;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class Examples extends OA\Examples
{
    /**
     * @param string|class-string|object|null $ref
     * @param array<string,mixed>|null        $x
     * @param Attachable[]|null               $attachables
     */
    public function __construct(
        ?string $example = null,
        ?string $summary = Generator::UNDEFINED,
        ?string $description = Generator::UNDEFINED,
        int|string|array|null $value = null,
        ?string $externalValue = null,
        string|object|null $ref = null,
        // annotation
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
            'example' => $example ?? Generator::UNDEFINED,
            'summary' => $summary,
            'description' => $description,
            'value' => $value ?? Generator::UNDEFINED,
            'externalValue' => $externalValue ?? Generator::UNDEFINED,
            'ref' => $ref ?? Generator::UNDEFINED,
            'x' => $x ?? Generator::UNDEFINED,
            'attachables' => $attachables ?? Generator::UNDEFINED,
        ]);
    }
}
