<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Spec;

use OpenApi\Spec as OA;
use OpenApi\Tests\Concerns\CollectsSpecClasses;
use PHPUnit\Framework\TestCase;

final class ComponentArgumentTest extends TestCase
{
    use CollectsSpecClasses;

    /**
     * A subclass that drops `component` leaves its users the deprecated per-type key, or
     * `Unknown named parameter $component` and a skipped class.
     */
    public function testSubclassesAcceptComponentWhereTheirParentDoes(): void
    {
        $failures = [];

        foreach (self::allSpecClasses() as $class) {
            // a reference to a schema, never a component itself
            if ($class === OA\Schema\Ref::class) {
                continue;
            }

            $parent = get_parent_class($class);
            if ($parent === false || !$this->takesComponent($parent)) {
                continue;
            }

            if (!$this->takesComponent($class)) {
                $failures[] = sprintf('%s::__construct() takes no $component, but %s does', $class, $parent);
            }
        }

        $this->assertEmpty($failures, implode("\n", $failures));
    }

    private function takesComponent(string $class): bool
    {
        $constructor = (new \ReflectionClass($class))->getConstructor();

        foreach ($constructor?->getParameters() ?? [] as $parameter) {
            if ($parameter->getName() === 'component') {
                return true;
            }
        }

        return false;
    }
}
