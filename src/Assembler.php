<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi;

use OpenApi\Utils\SourceLocation;

/**
 * Collects OpenAPI spec attributes from PHP reflectors and assembles them into a Specification.
 *
 * The assembler operates in two passes:
 *
 * 1. Stack-resolve (resolveNesting): On each reflector, sibling attributes are merged
 *    using merge() — e.g., a Schema adjacent to a Property on the same parameter
 *    fills the Property's $schema slot.
 *
 * 2. Hierarchical absorb (resolveHierarchy): Attributes from inner reflectors (properties,
 *    parameters, constants) flow up into enclosing-level containers using contains() —
 *    e.g., Property instances from class members are absorbed into the class-level Schema.
 *
 * After both passes, only root attributes (isRoot() = true) should remain.
 */
class Assembler
{
    public function __construct(
        protected Specification $specification = new Specification(),
    ) {
    }

    public function getSpecification(): Specification
    {
        return $this->specification;
    }

    /**
     * Collect all OpenAPI attributes from the given reflectors into the specification.
     */
    public function collect(\ReflectionClass|\ReflectionMethod|\ReflectionProperty|\ReflectionParameter|\ReflectionClassConstant ...$reflectors): static
    {
        foreach ($reflectors as $reflector) {
            $this->collectFromReflector($reflector);
        }

        return $this;
    }

    /**
     * Instantiate and resolve OpenAPI attributes from a single reflector.
     *
     * @return list<AttributeInterface>
     */
    public function instantiate(\ReflectionClass|\ReflectionMethod|\ReflectionProperty|\ReflectionParameter|\ReflectionClassConstant $reflector): array
    {
        if ($reflector instanceof \ReflectionMethod) {
            $outer = $this->resolveNesting($this->readAttributesArray($reflector));
            $inner = [];
            foreach ($reflector->getParameters() as $parameter) {
                array_push($inner, ...$this->resolveNesting($this->readAttributesArray($parameter)));
            }

            return $this->resolveHierarchy($outer, $inner);
        }

        return $this->resolveNesting($this->readAttributesArray($reflector));
    }

    protected function collectFromReflector(\ReflectionClass|\ReflectionMethod|\ReflectionProperty|\ReflectionParameter|\ReflectionClassConstant $reflector): void
    {
        if ($reflector instanceof \ReflectionClass) {
            $this->collectFromClass($reflector);

            return;
        }

        if ($reflector instanceof \ReflectionMethod) {
            $outer = $this->resolveNesting($this->readAttributesArray($reflector));
            $inner = [];
            foreach ($reflector->getParameters() as $parameter) {
                array_push($inner, ...$this->resolveNesting($this->readAttributesArray($parameter)));
            }
            $roots = $this->resolveHierarchy($outer, $inner);

            foreach ($roots as $root) {
                $this->specification->add($root);
            }

            return;
        }

        foreach ($this->resolveNesting($this->readAttributesArray($reflector)) as $root) {
            $this->specification->add($root);
        }
    }

    /**
     * Collect attributes from a class: stack-resolve at each structural level,
     * then hierarchically absorb inner attributes into class-level containers.
     */
    protected function collectFromClass(\ReflectionClass $class): void
    {
        $outer = $this->resolveNesting($this->readAttributesArray($class));

        $inner = [];

        foreach ($class->getProperties() as $property) {
            if ($property->isPromoted() || $property->getDeclaringClass()->getName() !== $class->getName()) {
                continue;
            }
            array_push($inner, ...$this->resolveNesting($this->readAttributesArray($property)));
        }

        $constructor = $class->getConstructor();
        if ($constructor) {
            foreach ($constructor->getParameters() as $parameter) {
                array_push($inner, ...$this->resolveNesting($this->readAttributesArray($parameter)));
            }
        }

        foreach ($class->getReflectionConstants() as $constant) {
            if ($constant->getDeclaringClass()->getName() !== $class->getName()) {
                continue;
            }
            array_push($inner, ...$this->resolveNesting($this->readAttributesArray($constant)));
        }

        $roots = $this->resolveHierarchy($outer, $inner);

        foreach ($roots as $root) {
            $this->specification->add($root);
        }

        foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->isConstructor() || $method->getDeclaringClass()->getName() !== $class->getName()) {
                continue;
            }
            $this->collectFromReflector($method);
        }
    }

    /**
     * Stack-resolve: merge sibling attributes on the same reflector using merge().
     *
     * For each attribute that declares merge targets, find a matching sibling and
     * nest into it via nestChild(). Returns the remaining unmerged attributes.
     *
     * @param  list<AttributeInterface> $attributes
     * @return list<AttributeInterface>
     */
    protected function resolveNesting(array $attributes): array
    {
        if (count($attributes) <= 1) {
            return $attributes;
        }

        $merged = [];

        foreach ($attributes as $index => $attribute) {
            $mergeTargets = $attribute->merge();

            if ($mergeTargets === []) {
                continue;
            }

            $matchingTarget = null;
            $matchingSlot = null;
            foreach ($attributes as $candidateIndex => $candidate) {
                if ($candidateIndex === $index || isset($merged[$candidateIndex])) {
                    continue;
                }

                foreach ($mergeTargets as $targetClass => $slot) {
                    if ($candidate instanceof $targetClass) {
                        if ($matchingTarget instanceof AttributeInterface) {
                            throw OpenApiException::fromSource(
                                sprintf('Ambiguous merge: %s matches multiple siblings on the same target', $attribute::class),
                                $attribute->getSourceLocation(),
                            );
                        }
                        $matchingTarget = $candidate;
                        $matchingSlot = $slot;
                    }
                }
            }

            if ($matchingTarget instanceof AttributeInterface) {
                $this->nestChild($matchingTarget, $attribute, $matchingSlot);
                $merged[$index] = true;
            }
        }

        $roots = [];
        foreach ($attributes as $index => $attribute) {
            if (!isset($merged[$index])) {
                $roots[] = $attribute;
            }
        }

        return $roots;
    }

    /**
     * Hierarchical absorb: outer-level attributes absorb inner-level attributes using contains().
     *
     * Each inner attribute is matched to an outer attribute that declares it in contains().
     * After absorption, validates that only root attributes remain.
     *
     * @param  list<AttributeInterface> $outer Attributes from the enclosing reflector (already stack-resolved)
     * @param  list<AttributeInterface> $inner Attributes from inner reflectors (already stack-resolved per-reflector)
     * @return list<AttributeInterface> Final root attributes
     */
    protected function resolveHierarchy(array $outer, array $inner): array
    {
        foreach ($inner as $innerAttribute) {
            $matchingContainer = null;
            $matchingSlot = null;

            foreach ($outer as $outerAttribute) {
                $containsTypes = $outerAttribute->contains();
                if ($containsTypes === []) {
                    continue;
                }

                foreach ($containsTypes as $childClass => $slot) {
                    if ($innerAttribute instanceof $childClass) {
                        if ($matchingContainer instanceof AttributeInterface) {
                            throw OpenApiException::fromSource(
                                sprintf(
                                    'Ambiguous hierarchy: %s matches multiple containers (%s and %s)',
                                    $innerAttribute::class,
                                    $matchingContainer::class,
                                    $outerAttribute::class,
                                ),
                                $innerAttribute->getSourceLocation(),
                            );
                        }
                        $matchingContainer = $outerAttribute;
                        $matchingSlot = $slot;
                        break;
                    }
                }
            }

            if ($matchingContainer === null) {
                throw OpenApiException::fromSource(
                    sprintf('Orphan attribute: %s has no valid container at enclosing level', $innerAttribute::class),
                    $innerAttribute->getSourceLocation(),
                );
            }

            $this->nestChild($matchingContainer, $innerAttribute, $matchingSlot);
        }

        // Validate: only roots should remain
        foreach ($outer as $attribute) {
            if (!$attribute->isRoot()) {
                throw OpenApiException::fromSource(
                    sprintf('Non-root attribute %s remains after resolution — it should have been absorbed by a container', $attribute::class),
                    $attribute->getSourceLocation(),
                );
            }
        }

        return $outer;
    }

    /**
     * Nest a child attribute into the named property of its parent.
     */
    protected function nestChild(AttributeInterface $parent, AttributeInterface $child, string $slot): void
    {
        if (str_ends_with($slot, '[]')) {
            $property = substr($slot, 0, -2);
            $current = $parent->{$property} ?? [];
            $current[] = $child;
            $parent->{$property} = $current;
        } else {
            $parent->{$slot} = $child;
        }
    }

    /**
     * @return list<AttributeInterface>
     */
    protected function readAttributesArray(\ReflectionClass|\ReflectionMethod|\ReflectionProperty|\ReflectionParameter|\ReflectionClassConstant $reflector): array
    {
        return iterator_to_array($this->readAttributes($reflector), false);
    }

    /**
     * @return \Generator<AttributeInterface>
     */
    protected function readAttributes(\ReflectionClass|\ReflectionMethod|\ReflectionProperty|\ReflectionParameter|\ReflectionClassConstant $reflector): \Generator
    {
        $attributes = $reflector->getAttributes(
            AttributeInterface::class,
            \ReflectionAttribute::IS_INSTANCEOF,
        );

        foreach ($attributes as $attribute) {
            if (!class_exists($attribute->getName())) {
                continue;
            }

            try {
                $instance = $attribute->newInstance();
            } catch (\Error $e) {
                throw OpenApiException::fromSource(
                    sprintf('Failed to instantiate attribute "%s": %s', $attribute->getName(), $e->getMessage()),
                    SourceLocation::fromReflector($reflector),
                    $e,
                );
            }

            $instance->setReflector($reflector);

            yield $instance;
        }
    }
}
