<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Annotations;

use OpenApi\Annotations as OA;
use OpenApi\Tests\OpenApiTestCase;

class AnnotationPropertiesDefinedTest extends OpenApiTestCase
{
    /**
     * @dataProvider allAnnotationClasses
     */
    public function testPropertiesAreNotUndefined(string $annotation): void
    {
        $properties = get_class_vars($annotation);
        $skip = OA\AbstractAnnotation::$_blacklist;
        foreach ($properties as $property => $value) {
            if (in_array($property, $skip)) {
                continue;
            }
            if ($value === null) {
                $this->fail('Property ' . basename($annotation) . '->' . $property . ' should be DEFINED');
            }
        }
    }
}
