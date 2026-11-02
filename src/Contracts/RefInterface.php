<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Contracts;

use OpenApi\Spec as OA;
use OpenApi\Specification\ComponentKey;

/**
 * A spec attribute that can reference a component through its `ref`.
 *
 * The ref is a string, a JSON pointer or a class name, or a `Schema\Ref` around one.
 */
interface RefInterface extends AttributeInterface
{
    /**
     * The ref as a string, a `Schema\Ref` around it unwrapped, or null when there is none.
     */
    public function getRefValue(): ?string;

    /**
     * The key the ref looks its component up by, or null when the ref is a JSON pointer or missing.
     *
     * A class name is a plain key. A `Schema\Ref` adds its qualifiers.
     */
    public function getRefKey(): ?ComponentKey;

    public function setRef(string|OA\Schema\Ref|null $ref): static;
}
