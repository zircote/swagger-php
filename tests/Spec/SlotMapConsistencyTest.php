<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Spec;

use OpenApi\Tests\Concerns\CollectsSpecClasses;
use PHPUnit\Framework\TestCase;

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
}
