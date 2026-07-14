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
     * @param list<Attachable>|null    $attachables Reusable custom attachable attributes
     */
    public function __construct(
        public ?array $x = null,
        public ?array $attachables = null,
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

    public function getClassReflector(): ?\ReflectionClass
    {
        $reflector = $this->reflector;

        if ($reflector instanceof \ReflectionClass) {
            return $reflector;
        }

        if ($reflector instanceof \ReflectionMethod || $reflector instanceof \ReflectionProperty || $reflector instanceof \ReflectionClassConstant) {
            return $reflector->getDeclaringClass();
        }

        if ($reflector instanceof \ReflectionParameter) {
            $function = $reflector->getDeclaringFunction();

            return $function instanceof \ReflectionMethod ? $function->getDeclaringClass() : null;
        }

        return null;
    }

    public function getClassName(): ?string
    {
        return $this->getClassReflector()?->getName();
    }

    public function getShortClassName(): ?string
    {
        return $this->getClassReflector()?->getShortName();
    }

    public function setReflector(?\Reflector $reflector): static
    {
        $this->reflector = $reflector;
        $this->sourceLocation = null;

        return $this;
    }

    public function getSourceLocation(): SourceLocation
    {
        if (!$this->sourceLocation instanceof SourceLocation) {
            $this->sourceLocation = $this->reflector instanceof \Reflector
                ? SourceLocation::fromReflector($this->reflector)
                : new SourceLocation();
        }

        return $this->sourceLocation;
    }
}
