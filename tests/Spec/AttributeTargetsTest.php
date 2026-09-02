<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Spec;

use OpenApi\Tests\Concerns\CollectsSpecClasses;
use PHPUnit\Framework\TestCase;

final class AttributeTargetsTest extends TestCase
{
    use CollectsSpecClasses;

    /**
     * `\Attribute::IS_REPEATABLE` is not a target, so declaring it alone leaves an attribute
     * that PHP refuses in every position: "cannot target class (allowed targets: )".
     *
     * Such a class still works as a constructor argument, which is how fixtures tend to use
     * the nested ones, so nothing fails until somebody writes it as an attribute.
     */
    public function testEveryAttributeDeclaresAtLeastOneTarget(): void
    {
        $failures = [];

        foreach (self::allSpecClasses() as $class) {
            $declared = (new \ReflectionClass($class))->getAttributes(\Attribute::class);
            if ($declared === []) {
                continue;
            }

            $flags = $declared[0]->newInstance()->flags;
            if (($flags & \Attribute::TARGET_ALL) === 0) {
                $failures[] = sprintf('%s declares #[\Attribute] with no TARGET_* flag, so it cannot be used as an attribute anywhere', $class);
            }
        }

        $this->assertEmpty($failures, implode("\n", $failures));
    }

    /**
     * A subclass narrowing targets is fine — `Parameter\Path` drops `TARGET_PROPERTY` that
     * `Parameter` allows. Narrowing to nothing is what this catches, and it is how
     * `MediaType\Json` and `MediaType\Xml` ended up unusable while `MediaType` was correct.
     */
    public function testAttributeSubclassesKeepAtLeastOneParentTarget(): void
    {
        $failures = [];

        foreach (self::allSpecClasses() as $class) {
            $reflection = new \ReflectionClass($class);
            $parent = $reflection->getParentClass();
            if ($parent === false) {
                continue;
            }

            $own = $reflection->getAttributes(\Attribute::class);
            $inherited = $parent->getAttributes(\Attribute::class);
            if ($own === [] || $inherited === []) {
                continue;
            }

            $ownTargets = $own[0]->newInstance()->flags & \Attribute::TARGET_ALL;
            $parentTargets = $inherited[0]->newInstance()->flags & \Attribute::TARGET_ALL;
            if ($parentTargets !== 0 && ($ownTargets & $parentTargets) === 0) {
                $failures[] = sprintf('%s shares no target with %s, so it cannot be used where its parent can', $class, $parent->getName());
            }
        }

        $this->assertEmpty($failures, implode("\n", $failures));
    }
}
