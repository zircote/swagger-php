<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Annotations;

use OpenApi\Annotations as OA;
use OpenApi\Tests\OpenApiTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Test if the annotation class nesting parent/child relations are coherent.
 */
final class ValidateRelationsTest extends OpenApiTestCase
{
    #[DataProvider('allAnnotationClasses')]
    public function testAncestors(string $class): void
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
     * @param class-string<OA\AbstractAnnotation> $class
     */
    #[DataProvider('allAnnotationClasses')]
    public function testNested(string $class): void
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
