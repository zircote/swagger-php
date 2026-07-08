<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi;

use OpenApi\Utils\DocBlockParser;

/**
 * Collects OpenAPI spec attributes from PHP reflectors and assembles them into a Specification.
 */
class Assembler
{
    protected DocBlockParser $docBlockParser;

    public function __construct(
        protected Specification $specification = new Specification(),
    ) {
        $this->docBlockParser = new DocBlockParser();
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
     * Instantiate OpenAPI attributes from a single reflector, resolve nesting,
     * and return the root-level attributes.
     *
     * @return list<AttributeInterface>
     */
    public function instantiate(\ReflectionClass|\ReflectionMethod|\ReflectionProperty|\ReflectionParameter|\ReflectionClassConstant $reflector): array
    {
        $instances = [];

        foreach ($this->readAttributes($reflector) as $instance) {
            $instances[] = $instance;
        }

        if ($reflector instanceof \ReflectionMethod) {
            foreach ($reflector->getParameters() as $parameter) {
                foreach ($this->readAttributes($parameter) as $instance) {
                    $instances[] = $instance;
                }
            }
        }

        return $this->resolveNesting($instances);
    }

    protected function collectFromReflector(\ReflectionClass|\ReflectionMethod|\ReflectionProperty|\ReflectionParameter|\ReflectionClassConstant $reflector): void
    {
        $instances = [];

        foreach ($this->readAttributes($reflector) as $instance) {
            $instances[] = $instance;
        }

        if ($reflector instanceof \ReflectionMethod) {
            foreach ($reflector->getParameters() as $parameter) {
                foreach ($this->readAttributes($parameter) as $instance) {
                    $instances[] = $instance;
                }
            }
        }

        foreach ($this->resolveNesting($instances) as $root) {
            $this->specification->add($root);
        }

        if ($reflector instanceof \ReflectionClass) {
            $this->collectFromClass($reflector);
        }
    }

    protected function collectFromClass(\ReflectionClass $class): void
    {
        foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            $this->collectFromReflector($method);
        }

        foreach ($class->getProperties() as $property) {
            if ($property->isPromoted()) {
                continue;
            }
            $this->collectFromReflector($property);
        }
    }

    /**
     * Resolve nesting among a flat list of attributes from the same target.
     * Returns only root-level attributes (those not nested into a parent).
     *
     * @param  list<AttributeInterface> $instances
     * @return list<AttributeInterface>
     */
    protected function resolveNesting(array $instances): array
    {
        if (count($instances) <= 1) {
            return $instances;
        }

        $nested = [];

        foreach ($instances as $index => $instance) {
            $allowedParents = $instance->allowedParents();

            if ($allowedParents === null || $allowedParents === []) {
                continue;
            }

            $matchingParent = null;
            foreach ($instances as $candidateIndex => $candidate) {
                if ($candidateIndex === $index) {
                    continue;
                }

                foreach ($allowedParents as $parentClass) {
                    if ($candidate instanceof $parentClass) {
                        if ($matchingParent instanceof AttributeInterface) {
                            throw new \LogicException(sprintf(
                                'Ambiguous nesting: %s matches multiple parents on the same target.',
                                $instance::class,
                            ));
                        }
                        $matchingParent = $candidate;
                    }
                }
            }

            if ($matchingParent instanceof AttributeInterface) {
                $this->nestChild($matchingParent, $instance);
                $nested[$index] = true;
            }
        }

        $roots = [];
        foreach ($instances as $index => $instance) {
            if (!isset($nested[$index])) {
                $roots[] = $instance;
            }
        }

        return $roots;
    }

    /**
     * Nest a child attribute into the appropriate property of its parent.
     */
    protected function nestChild(AttributeInterface $parent, AttributeInterface $child): void
    {
        $childClass = $child::class;
        $parentRef = new \ReflectionObject($parent);

        foreach ($parentRef->getProperties(\ReflectionProperty::IS_PUBLIC) as $prop) {
            $type = $prop->getType();

            if ($type instanceof \ReflectionNamedType) {
                if (!$type->isBuiltin() && ($childClass === $type->getName() || is_a($childClass, $type->getName(), true))) {
                    $prop->setValue($parent, $child);

                    return;
                }
            }
        }

        // Fall back: check constructor parameter docblocks for array types
        $constructor = $parentRef->getConstructor();
        if ($constructor) {
            $docComment = $constructor->getDocComment();
            if ($docComment) {
                foreach ($constructor->getParameters() as $param) {
                    $paramName = $param->getName();
                    $itemTypes = $this->docBlockParser->getParamArrayItemTypes($docComment, $paramName);
                    if ($itemTypes === null) {
                        continue;
                    }
                    foreach ($itemTypes as $itemType) {
                        $fqcn = str_contains($itemType, '\\') ? $itemType : 'OpenApi\\Spec\\' . $itemType;
                        if ($childClass === $fqcn || is_a($childClass, $fqcn, true)) {
                            $current = $parent->{$paramName} ?? [];
                            $current[] = $child;
                            $parent->{$paramName} = $current;

                            return;
                        }
                    }
                }
            }
        }

        throw new \LogicException(sprintf(
            'Cannot nest %s into %s: no matching property found.',
            $childClass,
            $parent::class,
        ));
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

            $instance = $attribute->newInstance();

            $instance->setReflector($reflector);

            yield $instance;
        }
    }
}
