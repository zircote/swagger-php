<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi;

use OpenApi\Utils\AttributeFactory;

/**
 * Collects OpenAPI spec attributes from PHP reflectors and assembles them into a Specification.
 *
 * Resolution is purely attribute-relationship driven — no PHP structural semantics:
 *
 * 1. Merge (resolveNesting): sibling attributes on the same reflector are merged
 *    using merge() — e.g., a Schema adjacent to a Property fills the Property's $schema slot.
 *
 * 2. Absorb (resolveHierarchy): resolved attributes flow upward level by level using
 *    contains() (first match wins). Roots that aren't absorbed pass through to the spec.
 */
class Assembler
{
    public function __construct(
        protected Specification $specification = new Specification(),
        protected AttributeFactory $attributeFactory = new AttributeFactory(),
    ) {
    }

    public function getSpecification(): Specification
    {
        return $this->specification;
    }

    public function getAttributeFactory(): AttributeFactory
    {
        return $this->attributeFactory;
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
        $this->attributeFactory->resetTranslators();

        $resolved = $this->resolveReflector($reflector);

        $this->specification->add(...$resolved);
    }

    /**
     * @return list<AttributeInterface>
     */
    protected function resolveReflector(\ReflectionClass|\ReflectionMethod|\ReflectionProperty|\ReflectionParameter|\ReflectionClassConstant $reflector): array
    {
        if (!$reflector instanceof \ReflectionClass) {
            return $this->attributeFactory->fromReflector($reflector);
        }

        $outer = $this->attributeFactory->fromReflector($reflector);
        $inner = $this->attributeFactory->membersOf($reflector);

        if ($outer === [] && $inner === []) {
            return [];
        }

        $resolved = $this->attributeFactory->resolveHierarchy($outer, $inner);

        $roots = [];
        foreach ($resolved as $attribute) {
            if ($attribute->isRoot()) {
                $roots[] = $attribute;
            } elseif ($outer !== []) {
                throw OpenApiException::fromSource(
                    sprintf('Non-root attribute %s remains after resolution', $attribute::class),
                    $attribute->getSourceLocation(),
                );
            }
        }

        return $roots;
    }
}
