<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec\Schema;

use OpenApi\Spec as OA;
use OpenApi\Specification\ComponentKey;

/**
 * A reference-only schema — $ref is required, most other Schema properties are unavailable.
 *
 * In OpenAPI 3.1+, $ref can be combined with title and description to override
 * the referenced schema's metadata without duplicating the definition.
 *
 * Usage:
 * ```php
 * #[OA\Property(schema: new OA\Schema\Ref(ref: Pet::class))]
 * #[OA\Property(schema: new OA\Schema\Ref(ref: '#/components/schemas/Pet', title: 'The pet'))]
 * #[OA\Property(schema: new OA\Schema\Ref(ref: Pet::class, description: 'Override desc'))]
 * ```
 *
 * If used on a `$ref` directly, only the ref value and the qualifiers are used.
 *
 * A ref given as a class can carry qualifiers. A class can be described more than once, such as
 * once per serialization group, and the qualifiers say which description the ref means. They
 * have to match the qualifiers that description was registered with:
 * ```php
 * #[OA\Property(schema: new OA\Schema\Ref(ref: Author::class, qualifiers: ['groups' => ['light']]))]
 * ```
 */
#[\Attribute(\Attribute::TARGET_ALL | \Attribute::IS_REPEATABLE)]
class Ref extends OA\Schema
{
    /**
     * @var array<string, scalar|list<scalar>>
     */
    protected array $qualifiers;

    /**
     * @param array<string, scalar|list<scalar>>|null $qualifiers Which description of the class to reference, when it has more than one
     */
    public function __construct(
        string $ref,
        ?string $title = null,
        ?string $description = null,
        ?array $x = null,
        ?array $attachables = null,
        ?OA\Fan $fan = null,
        ?array $qualifiers = null,
    ) {
        $this->qualifiers = $qualifiers ?? [];

        parent::__construct(
            title: $title,
            description: $description,
            ref: $ref,
            x: $x,
            attachables: $attachables,
            fan: $fan,
        );
    }

    /**
     * The key the ref looks its component up by, or null when the ref is a JSON pointer.
     */
    public function getRefKey(): ?ComponentKey
    {
        return is_string($this->ref) && !str_starts_with($this->ref, '#/')
            ? new ComponentKey($this->ref, $this->qualifiers)
            : null;
    }
}
