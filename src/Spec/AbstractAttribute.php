<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

use OpenApi\AttributeInterface;

abstract class AbstractAttribute implements AttributeInterface
{
    protected ?\Reflector $reflector = null;

    /**
     * @param array<string,mixed>|null $x
     */
    public function __construct(
        public ?array $x = null,
    ) {
    }

    public function allowedParents(): ?array
    {
        return null;
    }

    public function getReflector(): ?\Reflector
    {
        return $this->reflector;
    }

    public function setReflector(?\Reflector $reflector): static
    {
        $this->reflector = $reflector;

        return $this;
    }
}
