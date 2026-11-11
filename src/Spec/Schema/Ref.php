<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec\Schema;

use OpenApi\Spec as OA;
use OpenApi\Specification\ComponentKey;
use OpenApi\Type\TypeArguments;

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
 *
 * `of` holds a generic class's type arguments, written as one type string with each class given
 * with its namespace. It is read in the spelling swagger-php writes them in, so
 * `string,\App\Customer` and `string, App\Customer` reference the same schema:
 * ```php
 * #[OA\Property(schema: new OA\Schema\Ref(ref: Envelope::class, qualifiers: ['of' => Customer::class]))]
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
        if (!is_string($this->ref) || str_starts_with($this->ref, '#/')) {
            return null;
        }

        $qualifiers = $this->qualifiers;
        $typeArguments = $qualifiers[ComponentKey::TYPE_ARGUMENTS] ?? null;
        if (is_string($typeArguments)) {
            $qualifiers[ComponentKey::TYPE_ARGUMENTS] = TypeArguments::normalise($this->ref, $typeArguments);
        }

        return new ComponentKey($this->ref, $qualifiers);
    }
}
