<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Annotations;

use OpenApi\Annotations as OA;
use OpenApi\Tests\OpenApiTestCase;

/**
 * Test if the annotation class nesting parent/child relations are coherent.
 */
class ValidateRelationsTest extends OpenApiTestCase
{
    /**
     * @dataProvider allAnnotationClasses
     *
     * @param string $class
     */
    public function testAncestors($class): void
    {
        foreach ($class::$_parents as $parent) {
            $found = false;
            foreach (array_keys($parent::$_nested) as $nestedClass) {
                if ($nestedClass === $class || is_subclass_of($class, $nestedClass)) {
                    $found = true;
                    break;
                }
            }
            if ($found === false) {
                $this->fail($class . ' not found in ' . $parent . "::\$_nested. Found:\n  " . implode("\n  ", array_keys($parent::$_nested)));
            }
        }
    }

    /**
     * @dataProvider allAnnotationClasses
     *
     * @param class-string<OA\AbstractAnnotation> $class
     */
    public function testNested($class): void
    {
        foreach (array_keys($class::$_nested) as $nestedClass) {
            $found = false;
            foreach ($nestedClass::$_parents as $parent) {
                if ($parent === $class || is_subclass_of($class, $parent)) {
                    $found = true;
                    break;
                }
            }
            if ($found === false) {
                $this->fail($class . ' not found in ' . $nestedClass . "::\$parent. Found:\n  " . implode("\n  ", $nestedClass::$_parents));
            }
        }
    }
}
