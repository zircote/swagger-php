<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Analysers;

use OpenApi\Analysers\AttributeAnnotationFactory;
use OpenApi\Tests\Fixtures\UsingAttributes;
use OpenApi\Tests\Fixtures\InvalidPropertyAttribute;
use OpenApi\Tests\OpenApiTestCase;

/**
 * @requires PHP 8.1
 */
class AttributeAnnotationFactoryTest extends OpenApiTestCase
{
    public function testReturnedAnnotationsCout(): void
    {
        $rc = new \ReflectionClass(UsingAttributes::class);

        $annotations = (new AttributeAnnotationFactory())->build($rc, $this->getContext());
        $this->assertCount(1, $annotations);
    }

    public function testErrorOnInvalidAttribute(): void
    {
        $instance = new InvalidPropertyAttribute();
        $rm = new \ReflectionMethod($instance, 'post');

        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('OpenApi\Attributes\Property::__construct(): Argument #8 ($required) must be of type ?array');

        (new AttributeAnnotationFactory())->build($rm, $this->getContext());
    }
}
