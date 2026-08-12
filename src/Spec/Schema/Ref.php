<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec\Schema;

use OpenApi\Spec as OA;

/**
 * A reference-only schema — $ref is required, most other Schema properties are unavailable.
 *
 * In OpenAPI 3.1+, $ref can be combined with title and description to override
 * the referenced schema's metadata without duplicating the definition.
 *
 * Usage:
 *   #[OA\Property(schema: new OA\Schema\Ref(ref: Pet::class))]
 *   #[OA\Property(schema: new OA\Schema\Ref(ref: '#/components/schemas/Pet', title: 'The pet'))]
 *   #[OA\Property(schema: new OA\Schema\Ref(ref: Pet::class, description: 'Override desc'))]
 *
 * If used on a `$ref` directly, only the ref value is used.
 */
#[\Attribute(\Attribute::TARGET_ALL | \Attribute::IS_REPEATABLE)]
class Ref extends OA\Schema
{
    public function __construct(
        string $ref,
        ?string $title = null,
        ?string $description = null,
        ?array $x = null,
        ?array $attachables = null,
    ) {
        parent::__construct(
            title: $title,
            description: $description,
            ref: $ref,
            x: $x,
            attachables: $attachables,
        );
    }
}
