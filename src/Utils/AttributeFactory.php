<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Utils;

use OpenApi\Assembler\DefaultAttributeTranslator;
use OpenApi\Assembler\OperationMethodsTranslator;
use OpenApi\Assembler\OptionalPropertyAttributeTranslator;
use OpenApi\Contracts\AttributeInterface;
use OpenApi\Contracts\AttributeTranslatorInterface;
use OpenApi\OpenApiException;
use OpenApi\Spec as OA;
use OpenApi\Specification\ComponentName;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

/**
 * Creates spec attribute instances from PHP reflectors.
 *
 * Encapsulates: reading PHP attributes, stack-resolving siblings (merge),
 * and hierarchical absorb (contained). Shared between the Assembler (initial
 * collection) and augmenter pipes that need to manufacture spec objects
 * from reflection (e.g. Augmenter\Inheritance for non-schema ancestor members).
 */
class AttributeFactory implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var TypedList<AttributeTranslatorInterface>
     */
    protected TypedList $translators;

    public function __construct(protected TokenScanner $tokenScanner = new TokenScanner())
    {
        /** @var list<AttributeTranslatorInterface> $translators */
        $translators = [
            new DefaultAttributeTranslator(),
            new OptionalPropertyAttributeTranslator(),
            new OperationMethodsTranslator(),
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
     * Reset all translators, and give those that log this factory's logger.
     *
     * Called at the start of each top-level collection unit.
     */
    public function resetTranslators(): void
    {
        foreach ($this->translators as $translator) {
            if ($translator instanceof LoggerAwareInterface && $this->logger instanceof LoggerInterface) {
                $translator->setLogger($this->logger);
            }

            $translator->reset();
        }
    }

    /**
     * Read and resolve attributes from a single member reflector.
     *
     * For methods, also resolves parameter-level attributes into the method-level ones.
     *
     * @param \ReflectionClass<object>|\ReflectionMethod|\ReflectionProperty|\ReflectionParameter|\ReflectionClassConstant $reflector
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
     * @param \ReflectionClass<object> $class
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

            $this->reportConstructorAttributes($class, $constructor);
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
     *
     * @param \ReflectionClass<object>|\ReflectionMethod|\ReflectionProperty|\ReflectionParameter|\ReflectionClassConstant $reflector
     */
    public function hasAttributes(\ReflectionClass|\ReflectionMethod|\ReflectionProperty|\ReflectionParameter|\ReflectionClassConstant $reflector): bool
    {
        return $this->readAttributes($reflector) !== [];
    }

    /**
     * Get methods directly implemented by a class (not inherited from parents).
     *
     * @param \ReflectionClass<object> $class
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
     * @param \ReflectionClass<object> $class
     *
     * @return list<\ReflectionClass<object>>
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
     * @param \ReflectionClass<object> $class
     *
     * @return list<\ReflectionClass<object>>
     */
    public function getDirectTraits(\ReflectionClass $class): array
    {
        $scannerDetails = $this->tokenScanner->detailsFor($class);

        if ($scannerDetails !== null) {
            return array_values(array_filter(
                array_map(
                    fn (string $name): ?\ReflectionClass => class_exists($name) || trait_exists($name) ? new \ReflectionClass($name) : null,
                    $scannerDetails['traits'],
                ),
            ));
        }

        return [];
    }

    /**
     * Hierarchical absorb: inner-level attributes declare which outer-level types can contain them.
     *
     * An inner attribute matching one outer attribute via contained() is nested into it. Matching
     * more than one is an error, unless the inner attribute fans out to all of them. Inner
     * attributes that find no container pass through alongside outer.
     *
     * @param  list<AttributeInterface> $outer
     * @param  list<AttributeInterface> $inner
     * @return list<AttributeInterface>
     */
    public function resolveHierarchy(array $outer, array $inner): array
    {
        foreach ($inner as $innerAttribute) {
            $matches = [];
            foreach ($outer as $outerAttribute) {
                $match = $this->matchTarget($innerAttribute, $outerAttribute, $innerAttribute->contained());
                if ($match !== null) {
                    $matches[] = $match;
                }
            }

            if ($matches === []) {
                $outer[] = $innerAttribute;
                continue;
            }

            $this->nestIntoMatches($innerAttribute, $matches, 'Ambiguous hierarchy: %s matches multiple containers');
        }

        return $outer;
    }

    /**
     * A component declared on a member is one only in a class carrying `OA\Components`. Without
     * it, the key alone does not say which is meant: on a property the same schema is the
     * property's own, and on a method it would be a component. So a keyed component left over
     * from a member is dropped, and said so.
     *
     * A schema on a property arrives inside the `OA\Property` the shortcut adds, so its key is
     * looked for there. Unkeyed attributes are left alone: a parent class without a schema leaves
     * its properties unabsorbed on purpose, for `Augmenter\Inheritance` to read again.
     *
     * @param list<AttributeInterface> $outer    the class's own attributes
     * @param list<AttributeInterface> $resolved the class's attributes after absorbing its members
     *
     * @return list<AttributeInterface>
     */
    public function withoutStrayComponents(array $outer, array $resolved): array
    {
        foreach ($outer as $attribute) {
            if ($attribute instanceof OA\Components) {
                return $resolved;
            }
        }

        $kept = [];
        foreach ($resolved as $attribute) {
            $component = $attribute instanceof OA\Property ? $attribute->schema : $attribute;
            $key = $component instanceof AttributeInterface && ($component->isRoot() || $attribute instanceof OA\Property)
                ? ComponentName::of($component)
                : null;

            if ($key === null || in_array($attribute, $outer, true)) {
                $kept[] = $attribute;
                continue;
            }

            $this->logger?->warning(sprintf(
                '%s "%s" ignored, a component declared on a member needs %s on its class, in %s',
                $component::class,
                $key,
                OA\Components::class,
                $attribute->getSourceLocation(),
            ));
        }

        return $kept;
    }

    /**
     * The constructor is not a place to declare anything: only its parameters, as promoted
     * properties, are read. What sits on it is ignored, and said so; the class takes the same
     * attributes.
     *
     * @param \ReflectionClass<object> $class
     */
    protected function reportConstructorAttributes(\ReflectionClass $class, \ReflectionMethod $constructor): void
    {
        // inherited or brought in by a trait, it is reported where it is declared
        $scannerDetails = $this->tokenScanner->detailsFor($class);
        if ($constructor->getDeclaringClass()->getName() !== $class->getName()
            || ($scannerDetails && !in_array($constructor->getName(), $scannerDetails['methods'], true))
        ) {
            return;
        }

        $attributes = $constructor->getAttributes(AttributeInterface::class, \ReflectionAttribute::IS_INSTANCEOF);
        if ($attributes === []) {
            return;
        }

        $this->logger?->warning(sprintf(
            '%s ignored on a constructor, declare %s on the class in %s',
            implode(', ', array_map(static fn (\ReflectionAttribute $attribute): string => $attribute->getName(), $attributes)),
            count($attributes) === 1 ? 'it' : 'them',
            SourceLocation::fromReflector($constructor),
        ));
    }

    /**
     * Stack-resolve: merge sibling attributes on the same reflector using merge().
     *
     * Chains resolve inner-to-outer regardless of declaration order: an attribute is
     * held back while another sibling still wants to merge into it, so a MediaType
     * finds its Response before the Response is folded into the Operation. Mutual
     * targets — possible with custom attachables, not among the native attributes —
     * cannot be ordered that way and resolve in declaration order instead.
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

        $pending = [];
        foreach ($attributes as $index => $attribute) {
            if ($attribute->merge() !== []) {
                $pending[$index] = true;
            }
        }

        while ($pending !== []) {
            $progress = false;

            foreach (array_keys($pending) as $index) {
                if ($this->isMergeTarget($attributes, $pending, $index)) {
                    continue;
                }

                $this->mergeIntoSibling($attributes, $index, $merged);
                unset($pending[$index]);
                $progress = true;
            }

            if (!$progress) {
                $index = array_key_first($pending);
                $this->mergeIntoSibling($attributes, $index, $merged);
                unset($pending[$index]);
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
     * Whether another pending sibling names this attribute's type as a merge target.
     *
     * @param list<AttributeInterface> $attributes
     * @param array<int,true>          $pending
     */
    protected function isMergeTarget(array $attributes, array $pending, int $index): bool
    {
        foreach (array_keys($pending) as $otherIndex) {
            if ($otherIndex === $index) {
                continue;
            }

            foreach (array_keys($attributes[$otherIndex]->merge()) as $targetClass) {
                if ($attributes[$index] instanceof $targetClass) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Merge one attribute into its matching unmerged sibling. Matching more than one is an error,
     * unless the attribute fans out to all of them.
     *
     * @param list<AttributeInterface> $attributes
     * @param array<int,true>          $merged
     */
    protected function mergeIntoSibling(array $attributes, int $index, array &$merged): void
    {
        $attribute = $attributes[$index];

        $matches = [];
        foreach ($attributes as $candidateIndex => $candidate) {
            if ($candidateIndex === $index || isset($merged[$candidateIndex])) {
                continue;
            }

            $match = $this->matchTarget($attribute, $candidate, $attribute->merge());
            if ($match !== null) {
                $matches[] = $match;
            }
        }

        if ($matches !== []) {
            $this->nestIntoMatches($attribute, $matches, 'Ambiguous merge: %s matches multiple siblings on the same target');
            $merged[$index] = true;
        }
    }

    /**
     * Nest a child into the one parent it matched, or with `Fan::All` into each of several that
     * match by the same target type: the child itself into the first, a copy into every other.
     * Several matches are an error otherwise, and so are matches of different target types,
     * whatever the child's `fan`.
     *
     * @param non-empty-list<array{AttributeInterface, string, list<class-string>}> $matches parent, slot and the target types it matched by
     */
    protected function nestIntoMatches(AttributeInterface $child, array $matches, string $ambiguous): void
    {
        if (count($matches) > 1) {
            // one type when a target type is shared by every match, whichever order they come in
            $shared = array_intersect(...array_column($matches, 2));
            $fanned = $child->getFan() === OA\Fan::All;

            if (!$fanned || $shared === []) {
                throw OpenApiException::fromSource(
                    sprintf(
                        '%s (%s). %s',
                        sprintf($ambiguous, $child::class),
                        implode(', ', array_map($this->matchLabel(...), array_column($matches, 0))),
                        $fanned
                            ? 'Fan::All only puts it into several attributes of one type. Nest it explicitly in the one it belongs to.'
                            : 'Nest it explicitly in the one it belongs to, give it a `component` and reference it from each, or set `fan: Fan::All` to put it in all of them.',
                    ),
                    $child->getSourceLocation(),
                );
            }
        }

        foreach ($matches as $position => [$parent, $slot]) {
            $this->nestChild($parent, $position === 0 ? $child : $child->copy(), $slot);
        }
    }

    /**
     * The slot a child takes in a candidate parent, with the target types of `$targets` the
     * candidate is an instance of, or null when it is none of them. A candidate matching
     * several target types that name different slots is an error: the slot would be decided by
     * the order of `$targets`.
     *
     * @param array<class-string, string> $targets target type and slot, from `merge()` or `contained()`
     *
     * @return array{AttributeInterface, string, list<class-string>}|null
     */
    protected function matchTarget(AttributeInterface $child, AttributeInterface $candidate, array $targets): ?array
    {
        $matched = array_filter($targets, static fn (string $slot, string $type): bool => $candidate instanceof $type, ARRAY_FILTER_USE_BOTH);
        if ($matched === []) {
            return null;
        }

        $slots = array_values(array_unique($matched));
        if (count($slots) > 1) {
            throw OpenApiException::fromSource(
                sprintf(
                    'Ambiguous slot: %s matches %s as more than one target type (%s), and they name different slots (%s)',
                    $child::class,
                    $this->matchLabel($candidate),
                    implode(', ', array_keys($matched)),
                    implode(', ', $slots),
                ),
                $child->getSourceLocation(),
            );
        }

        return [$candidate, $slots[0], array_keys($matched)];
    }

    /**
     * An attribute as an ambiguity message names it: its class, and its component key if it has one.
     */
    protected function matchLabel(AttributeInterface $attribute): string
    {
        $component = ComponentName::isComponentType($attribute) ? ComponentName::of($attribute) : null;

        return $component !== null ? sprintf('%s "%s"', $attribute::class, $component) : $attribute::class;
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
     * @param \ReflectionClass<object>|\ReflectionMethod|\ReflectionProperty|\ReflectionParameter|\ReflectionClassConstant $reflector
     *
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
