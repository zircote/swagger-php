<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Attributes;

use OpenApi\Annotations as OA;
use OpenApi\Undefined;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class Tag extends OA\Tag
{
    /**
     * @param array<string,mixed>|null $x
     * @param list<Attachable>|null    $attachables
     */
    public function __construct(
        ?string $name = null,
        ?string $description = Undefined::UNDEFINED,
        ?string $summary = Undefined::UNDEFINED,
        ?string $parent = null,
        ?string $kind = null,
        ?ExternalDocumentation $externalDocs = null,

        // abstract annotation
        ?array $x = null,
        ?array $attachables = null
    ) {
        parent::__construct([
                'name' => $name ?? Undefined::UNDEFINED,
                'description' => $description,
                'summary' => $summary,
                'parent' => $parent ?? Undefined::UNDEFINED,
                'kind' => $kind ?? Undefined::UNDEFINED,
                'x' => $x ?? Undefined::UNDEFINED,
                'attachables' => $attachables ?? Undefined::UNDEFINED,
                'value' => $this->combine($externalDocs),
            ]);
    }
}
