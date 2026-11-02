<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec\Concerns;

use OpenApi\Spec\Schema;
use OpenApi\Specification\ComponentKey;

/**
 * `RefInterface` for an attribute that declares `public string|Schema\Ref|null $ref`.
 */
trait RefTrait
{
    public function getRefValue(): ?string
    {
        return $this->ref instanceof Schema\Ref ? $this->ref->getRefValue() : $this->ref;
    }

    public function getRefKey(): ?ComponentKey
    {
        if ($this->ref instanceof Schema\Ref) {
            return $this->ref->getRefKey();
        }

        return is_string($this->ref) && !str_starts_with($this->ref, '#/') ? new ComponentKey($this->ref) : null;
    }

    public function setRef(string|Schema\Ref|null $ref): static
    {
        $this->ref = $ref;

        return $this;
    }
}
