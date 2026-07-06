<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Annotations as OA;
use OpenApi\Undefined;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class Examples extends OA\Examples
{
    /**
     * @param string|class-string|object|null $ref
     * @param array<string,mixed>|null        $x
     * @param list<Attachable>|null           $attachables
     */
    public function __construct(
        ?string $example = null,
        ?string $summary = Undefined::UNDEFINED,
        ?string $description = Undefined::UNDEFINED,
        int|string|array|null $value = null,
        ?string $externalValue = null,
        string|object|null $ref = null,

        // abstract annotation
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
            'example' => $example ?? Undefined::UNDEFINED,
            'summary' => $summary,
            'description' => $description,
            'value' => $value ?? Undefined::UNDEFINED,
            'externalValue' => $externalValue ?? Undefined::UNDEFINED,
            'ref' => $ref ?? Undefined::UNDEFINED,
            'x' => $x ?? Undefined::UNDEFINED,
            'attachables' => $attachables ?? Undefined::UNDEFINED,
        ]);
    }
}
