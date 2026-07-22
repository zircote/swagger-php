<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Utils;

use OpenApi\Assembler\DefaultAttributeTranslator;
use OpenApi\AttributeInterface;
use OpenApi\AttributeTranslatorInterface;
use OpenApi\OpenApiException;
use OpenApi\Spec as OA;

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
     * @param callable(TypedList<AttributeTranslatorInterface>): void $hook
     */
    public function withTranslators(callable $hook): static
    {
        $hook($this->translators);

        return $this;
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

            return $this->resolveHierarchy($outer, $inner);
        }

        return $this->resolveNesting($this->readAttributes($reflector));
    }

    /**
     * Read and resolve attributes from a class's own members (properties, constructor params, constants).
     *
     * Does NOT include the class-level attributes themselves or methods.
     * Returns inner attributes suitable for hierarchical absorption into a class-level container.
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

        foreach ($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if ($method->isConstructor()
                || $method->getDeclaringClass()->getName() !== $class->getName()
                || ($scannerDetails && !in_array($method->getName(), $scannerDetails['methods'], true))
            ) {
                continue;
            }

            $resolved = $this->resolveNesting($this->readAttributes($method));
            foreach ($resolved as $attribute) {
                if ($attribute instanceof OA\Property) {
                    $inner[] = $attribute;
                }
            }
        }

        return $inner;
    }

    /**
     * Check whether a reflector has any OpenAPI attributes.
     */
    public function hasAttributes(\ReflectionClass|\ReflectionMethod|\ReflectionProperty|\ReflectionParameter|\ReflectionClassConstant $reflector): bool
    {
        return $this->readAttributes($reflector) !== [];
    }

    /**
     * Check whether a method only produces Property attributes (no operations).
     */
    public function hasOnlyProperties(\ReflectionMethod $method): bool
    {
        $attributes = $this->readAttributes($method);
        if ($attributes === []) {
            return false;
        }

        foreach ($attributes as $attribute) {
            if (!$attribute instanceof OA\Property && !$attribute instanceof OA\Schema) {
                return false;
            }
        }

        return true;
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

    /**
     * Hierarchical absorb: outer-level attributes absorb inner-level attributes using contains().
     *
     * @param  list<AttributeInterface> $outer
     * @param  list<AttributeInterface> $inner
     * @return list<AttributeInterface>
     */
    public function resolveHierarchy(array $outer, array $inner): array
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

                $attributes[] = $instance;
            }

            $attributes = $translator->translate($attributes);
        }

        return array_values(array_filter($attributes, static fn (object $item): bool => $item instanceof AttributeInterface));
    }
}
