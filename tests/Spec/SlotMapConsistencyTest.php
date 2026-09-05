<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Spec;

use OpenApi\Tests\Concerns\CollectsSpecClasses;
use PHPUnit\Framework\TestCase;
use Symfony\Component\TypeInfo\Type;
use Symfony\Component\TypeInfo\Type\CollectionType;
use Symfony\Component\TypeInfo\Type\ObjectType;
use Symfony\Component\TypeInfo\Type\UnionType;
use Symfony\Component\TypeInfo\TypeResolver\TypeResolver;

final class SlotMapConsistencyTest extends TestCase
{
    use CollectsSpecClasses;

    public function testMergeAndContainedSlotsAreConsistentWhenBothDeclared(): void
    {
        $failures = [];

        foreach (self::allSpecClasses() as $class) {
            $instance = (new \ReflectionClass($class))->newInstanceWithoutConstructor();
            $merge = $instance->merge();
            $contained = $instance->contained();

            foreach ($contained as $parentClass => $slot) {
                if (isset($merge[$parentClass]) && $merge[$parentClass] !== $slot) {
                    $failures[] = sprintf(
                        '%s declares %s in both merge() and contained() but with different slots: merge="%s", contained="%s"',
                        $class,
                        $parentClass,
                        $merge[$parentClass],
                        $slot
                    );
                }
            }
        }

        $this->assertEmpty($failures, implode("\n", $failures));
    }

    public function testSlotsNameRealPropertiesOnTheirTarget(): void
    {
        $failures = [];
        foreach (self::allSpecClasses() as $class) {
            $instance = (new \ReflectionClass($class))->newInstanceWithoutConstructor();

            foreach (['merge' => $instance->merge(), 'contained' => $instance->contained()] as $kind => $map) {
                foreach ($map as $target => $slot) {
                    if (!class_exists($target)) {
                        $failures[] = sprintf('%s::%s() targets unknown class %s', $class, $kind, $target);
                        continue;
                    }

                    $property = rtrim($slot, '[]');
                    if (!property_exists($target, $property)) {
                        $failures[] = sprintf(
                            '%s::%s() declares slot "%s" on %s, which has no such property',
                            $class,
                            $kind,
                            $slot,
                            $target
                        );
                    }
                }
            }
        }

        $this->assertEmpty($failures, implode("\n", $failures));
    }

    /**
     * A slot names a property on the target, so the target's own type has to admit the
     * class declaring the slot. `Schema` once named `Schema::$properties`, a `list<Property>`,
     * which crashed the inheritance augmenter as soon as anything reached it.
     */
    public function testSlotsAcceptTheDeclaringClass(): void
    {
        $typeResolver = TypeResolver::create();
        $failures = [];

        foreach (self::allSpecClasses() as $class) {
            $instance = (new \ReflectionClass($class))->newInstanceWithoutConstructor();

            foreach (['merge' => $instance->merge(), 'contained' => $instance->contained()] as $kind => $map) {
                foreach ($map as $target => $slot) {
                    $property = rtrim($slot, '[]');
                    if (!class_exists($target) || !property_exists($target, $property)) {
                        continue; // reported by testSlotsNameRealPropertiesOnTheirTarget
                    }

                    $accepted = self::objectTypesIn($typeResolver->resolve(new \ReflectionProperty($target, $property)));
                    if ($accepted === []) {
                        continue; // untyped or scalar slot: nothing to check against
                    }

                    foreach ($accepted as $candidate) {
                        if ($class === $candidate || is_subclass_of($class, $candidate)) {
                            continue 2;
                        }
                    }

                    $failures[] = sprintf(
                        '%s::%s() declares slot "%s" on %s, which accepts only %s',
                        $class,
                        $kind,
                        $slot,
                        $target,
                        implode(', ', $accepted)
                    );
                }
            }
        }

        $this->assertSame([], $failures, implode("\n", $failures));
    }

    /**
     * @return list<class-string>
     */
    protected static function objectTypesIn(Type $type): array
    {
        if ($type instanceof UnionType) {
            $types = [];
            foreach ($type->getTypes() as $member) {
                $types = [...$types, ...self::objectTypesIn($member)];
            }

            return $types;
        }

        if ($type instanceof CollectionType) {
            return self::objectTypesIn($type->getCollectionValueType());
        }

        return $type instanceof ObjectType ? [$type->getClassName()] : [];
    }
}
