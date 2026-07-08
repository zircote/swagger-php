<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Spec;

use OpenApi\AttributeInterface;
use OpenApi\Utils\SourceLocation;

abstract class AbstractAttribute implements AttributeInterface
{
    protected ?\Reflector $reflector = null;

    protected ?SourceLocation $sourceLocation = null;

    /**
     * @param array<string,mixed>|null $x
     */
    public function __construct(
        public ?array $x = null,
    ) {
    }

    public function isRoot(): bool
    {
        return false;
    }

    public function merge(): array
    {
        return [];
    }

    public function contains(): array
    {
        return [];
    }

    public function getReflector(): ?\Reflector
    {
        return $this->reflector;
    }

    public function setReflector(?\Reflector $reflector): static
    {
        $this->reflector = $reflector;
        $this->sourceLocation = null;

        return $this;
    }

    public function getSourceLocation(): SourceLocation
    {
        if ($this->sourceLocation === null) {
            $this->sourceLocation = $this->reflector !== null
                ? SourceLocation::fromReflector($this->reflector)
                : new SourceLocation();
        }

        return $this->sourceLocation;
    }
}
