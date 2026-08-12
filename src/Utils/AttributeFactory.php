<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Utils;

use OpenApi\Assembler\DefaultAttributeTranslator;
use OpenApi\AttributeInterface;
use OpenApi\AttributeTranslatorInterface;
use OpenApi\OpenApiException;

/**
 * Creates spec attribute instances from PHP reflectors.
 *
 * Encapsulates: reading PHP attributes, stack-resolving siblings (merge),
 * and hierarchical absorb (contains). Shared between the Assembler (initial
 * collection) and augmenter pipes that need to manufacture spec objects
 * from reflection (e.g. ExpandHierarchy for non-schema ancestor members).
 */
class AttributeFactory
{
    /**
     * @var TypedList<AttributeTranslatorInterface>
     */
    protected TypedList $translators;

    public function __construct(protected TokenScanner $tokenScanner = new TokenScanner())
    {
        /** @var list<AttributeTranslatorInterface> $translators */
        $translators = [
            new DefaultAttributeTranslator(),
        ];

        $this->translators = new TypedList($translators);
    }

    public function getTokenScanner(): TokenScanner
    {
        return $this->tokenScanner;
    }

    /**
     * @return TypedList<AttributeTranslatorInterface>
     */
    public function getTranslators(): TypedList
    {
        return $this->translators;
    }

    /**
     * @param TypedList<AttributeTranslatorInterface> $translators
     */
    public function setTranslators(TypedList $translators): static
    {
        $this->translators = $translators;

        return $this;
    }

    /**
     * @param callable(TypedList<AttributeTranslatorInterface>): (TypedList<AttributeTranslatorInterface>|void) $hook
     */
    public function withTranslators(callable $hook): static
    {
        $hook($this->translators);

        return $this;
    }

    /**
     * Reset all translators.
     *
     * Called at the start of each top-level collection unit.
     */
    public function resetTranslators(): void
    {
        foreach ($this->translators as $translator) {
            $translator->reset();
        }
    }

    /**
     * Read and resolve attributes from a single member reflector.
     *
     * For methods, also resolves parameter-level attributes into the method-level ones.
     *
     * @return list<AttributeInterface>
     */
    public function fromReflector(\ReflectionClass|\ReflectionMethod|\ReflectionProperty|\ReflectionParameter|\ReflectionClassConstant $reflector): array
    {
        if ($reflector instanceof \ReflectionMethod) {
            $outer = $this->resolveNesting($this->readAttributes($reflector));
            $inner = [];
            foreach ($reflector->getParameters() as $parameter) {
                array_push($inner, ...$this->resolveNesting($this->readAttributes($parameter)));
            }

            $resolved = $this->resolveHierarchy($outer, $inner);

            if ($outer !== []) {
                foreach ($resolved as $attribute) {
                    if (!$attribute->isRoot() && !in_array($attribute, $outer, true)) {
                        throw OpenApiException::fromSource(
                            sprintf('Orphan attribute: %s has no valid container at enclosing level', $attribute::class),
                            $attribute->getSourceLocation(),
                        );
                    }
                }
            }

            return $resolved;
        }

        return $this->resolveNesting($this->readAttributes($reflector));
    }

    /**
     * Read and resolve attributes from all class members (properties, constants, methods).
     *
     * Each member is fully resolved internally (merge + parameter absorption for methods),
     * then all results are returned for hierarchical absorption into class-level containers.
     *
     * @return list<AttributeInterface>
     */
    public function membersOf(\ReflectionClass $class): array
    {
        $inner = [];
        $scannerDetails = $this->tokenScanner->detailsFor($class);

        foreach ($class->getProperties() as $property) {
            if ($property->isPromoted()
                || $property->getDeclaringClass()->getName() !== $class->getName()
                || ($scannerDetails && !in_array($property->getName(), $scannerDetails['properties'], true))
            ) {
                continue;
            }

            array_push($inner, ...$this->resolveNesting($this->readAttributes($property)));
        }

        $constructor = $class->getConstructor();
        if ($constructor) {
            foreach ($constructor->getParameters() as $parameter) {
                array_push($inner, ...$this->resolveNesting($this->readAttributes($parameter)));
            }
        }

        foreach ($class->getReflectionConstants() as $constant) {
            if ($constant->getDeclaringClass()->getName() !== $class->getName()
                || ($scannerDetails && !in_array($constant->getName(), $scannerDetails['consts'], true))
            ) {
                continue;
            }

            array_push($inner, ...$this->resolveNesting($this->readAttributes($constant)));
        }

        foreach ($this->getDirectMethods($class) as $method) {
            array_push($inner, ...$this->fromReflector($method));
        }

        return $inner;
    }

    /**
     * Check whether a reflector has any `AttributeInterface` attributes.
     */
    public function hasAttributes(\ReflectionClass|\ReflectionMethod|\ReflectionProperty|\ReflectionParameter|\ReflectionClassConstant $reflector): bool
    {
        return $this->readAttributes($reflector) !== [];
    }

    /**
     * Get methods directly implemented by a class (not inherited from parents).
     *
     * @return list<\ReflectionMethod>
     */
    public function getDirectMethods(\ReflectionClass $class): array
    {
        $scannerDetails = $this->tokenScanner->detailsFor($class);

        $methods = [];
        foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->isConstructor()
                || $method->getDeclaringClass()->getName() !== $class->getName()
                || ($scannerDetails && !in_array($method->getName(), $scannerDetails['methods'], true))
            ) {
                continue;
            }

            $methods[] = $method;
        }

        return $methods;
    }

    /**
     * Get interfaces directly implemented by a class (not inherited from parents).
     *
     * @return list<\ReflectionClass>
     */
    public function getDirectInterfaces(\ReflectionClass $class): array
    {
        $interfaces = $class->getInterfaces();

        $parent = $class->getParentClass();
        if ($parent !== false) {
            $parentInterfaceNames = array_map(
                fn (\ReflectionClass $i): string => $i->getName(),
                $parent->getInterfaces(),
            );
            $interfaces = array_filter(
                $interfaces,
                fn (\ReflectionClass $i): bool => !in_array($i->getName(), $parentInterfaceNames, true),
            );
        }

        return array_values($interfaces);
    }

    /**
     * Get traits directly used by the given class (excludes inherited trait-uses).
     *
     * PHP's ReflectionClass::getTraits() flattens the entire trait tree, so we
     * must exclude traits that come from a parent class or from another trait's use.
     *
     * @return list<\ReflectionClass>
     */
    public function getDirectTraits(\ReflectionClass $class): array
    {
        $scannerDetails = $this->tokenScanner->detailsFor($class);

        if ($scannerDetails !== null) {
            return array_filter(
                array_map(
                    fn (string $name): ?\ReflectionClass => class_exists($name) || trait_exists($name) ? new \ReflectionClass($name) : null,
                    $scannerDetails['traits'],
                ),
            );
        }

        return [];
    }

    /**
     * Hierarchical absorb: outer-level attributes absorb inner-level attributes using contains().
     *
     * Inner attributes that match a container's contains() are nested into it (first match wins).
     * Inner attributes that find no container pass through alongside outer.
     *
     * @param  list<AttributeInterface> $outer
     * @param  list<AttributeInterface> $inner
     * @return list<AttributeInterface>
     */
    public function resolveHierarchy(array $outer, array $inner): array
    {
        foreach ($inner as $innerAttribute) {
            $absorbed = false;

            foreach ($outer as $outerAttribute) {
                $containsTypes = $outerAttribute->contains();
                if ($containsTypes === []) {
                    continue;
                }

                foreach ($containsTypes as $childClass => $slot) {
                    if ($innerAttribute instanceof $childClass) {
                        $this->nestChild($outerAttribute, $innerAttribute, $slot);
                        $absorbed = true;
                        break 2;
                    }
                }
            }

            if (!$absorbed) {
                $outer[] = $innerAttribute;
            }
        }

        return $outer;
    }

    /**
     * Stack-resolve: merge sibling attributes on the same reflector using merge().
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

    protected function nestChild(AttributeInterface $parent, AttributeInterface $child, string $slot): void
    {
        $validateSlot = function (AttributeInterface $parent, AttributeInterface $child, string $slot): void {
            if (!property_exists($parent, $slot)) {
                throw OpenApiException::fromSource(
                    sprintf('Invalid slot: "%s" not found in %s for child %s', $slot, $parent::class, $child::class),
                    $child->getSourceLocation(),
                );
            }

        };

        if (str_ends_with($slot, '[]')) {
            $property = substr($slot, 0, -2);

            $validateSlot($parent, $child, $property);

            $current = $parent->{$property} ?? [];
            $current[] = $child;
            $parent->{$property} = $current;
        } else {
            $validateSlot($parent, $child, $slot);

            if ($parent->{$slot} instanceof AttributeInterface) {
                throw OpenApiException::fromSource(
                    sprintf('Duplicate merge: %s already has a %s in slot "%s"', $parent::class, $parent->{$slot}::class, $slot),
                    $child->getSourceLocation(),
                );
            }
            $parent->{$slot} = $child;
        }
    }

    /**
     * @return list<AttributeInterface>
     */
    protected function readAttributes(\ReflectionClass|\ReflectionMethod|\ReflectionProperty|\ReflectionParameter|\ReflectionClassConstant $reflector): array
    {
        $attributes = [];

        foreach ($this->translators as $translator) {
            $current = [];

            foreach ($translator->getAttributes($reflector) as $attribute) {
                try {
                    $instance = $attribute->newInstance();
                } catch (\Error $e) {
                    throw OpenApiException::fromSource(
                        sprintf('Failed to instantiate attribute "%s": %s', $attribute->getName(), $e->getMessage()),
                        SourceLocation::fromReflector($reflector),
                        $e,
                    );
                }

                if ($instance instanceof AttributeInterface) {
                    $instance->setReflector($reflector);
                }

                $current[] = $instance;
            }

            $attributes = $translator->translate($attributes, $current, $reflector);
        }

        // final pass in case translators didn't set reflector
        foreach ($attributes as $item) {
            if ($item instanceof AttributeInterface && !$item->getReflector() instanceof \Reflector) {
                $item->setReflector($reflector);
            }
        }

        return array_values(array_filter($attributes, static fn (object|null $item): bool => $item instanceof AttributeInterface));
    }
}
