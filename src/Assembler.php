<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi;

use OpenApi\Utils\AttributeFactory;
use OpenApi\Utils\TokenScanner;

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
        protected AttributeFactory $factory = new AttributeFactory(),
        protected TokenScanner $tokenScanner = new TokenScanner(),
    ) {
    }

    public function getSpecification(): Specification
    {
        return $this->specification;
    }

    public function getFactory(): AttributeFactory
    {
        return $this->factory;
    }

    public function getTokenScanner(): TokenScanner
    {
        return $this->tokenScanner;
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

    protected function collectFromReflector(\ReflectionClass|\ReflectionMethod|\ReflectionProperty|\ReflectionParameter|\ReflectionClassConstant $reflector): void
    {
        if ($reflector instanceof \ReflectionClass) {
            $this->collectFromClass($reflector);

            return;
        }

        foreach ($this->factory->fromReflector($reflector) as $root) {
            $this->specification->add($root);
        }
    }

    /**
     * Collect attributes from a class: stack-resolve at each structural level,
     * then hierarchically absorb inner attributes into class-level containers.
     */
    protected function collectFromClass(\ReflectionClass $class): void
    {
        $outer = $this->factory->fromReflector($class);

        if ($outer !== []) {
            // Only collect own members when the class has a root attribute (e.g. Schema).
            // Classes without root attributes (plain parents/traits) are handled later
            // by ExpandHierarchy which merges their members into the child schema.
            $inner = $this->factory->membersOf($class);
            $roots = $this->factory->resolveHierarchy($outer, $inner);

            foreach ($roots as $root) {
                $this->specification->add($root);
            }
        }

        // Methods are always processed — a controller may have operations without
        // any class-level schema attribute. Properties on methods are handled via
        // membersOf() (absorbed into class schema) or ExpandHierarchy (non-schema interfaces).
        foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->isConstructor() || $method->getDeclaringClass()->getName() !== $class->getName()) {
                continue;
            }
            if ($this->factory->hasOnlyProperties($method)) {
                continue;
            }
            foreach ($this->factory->fromReflector($method) as $root) {
                $this->specification->add($root);
            }
        }
    }
}
