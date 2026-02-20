<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Analysers;

use OpenApi\Analysers\AttributeAnnotationFactory;
use OpenApi\Attributes\Property;
use OpenApi\Tests\Fixtures\InvalidPropertyAttribute;
use OpenApi\Tests\Fixtures\UsingAttributes;
use OpenApi\Tests\OpenApiTestCase;

final class AttributeAnnotationFactoryTest extends OpenApiTestCase
{
    public function testReturnedAnnotationsCount(): void
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
        $this->expectExceptionMessage(Property::class . '::__construct(): Argument #9 ($required) must be of type ?array');

        (new AttributeAnnotationFactory())->build($rm, $this->getContext());
    }

    public function testIgnoreOtherAttributes(): void
    {
        $rc = new \ReflectionClass(UsingAttributes::class);

        (new AttributeAnnotationFactory())->build($rc, $context = $this->getContext());
        $this->assertIsArray($context->other);
        $this->assertCount(1, $context->other);

        (new AttributeAnnotationFactory(true))->build($rc, $context = $this->getContext());
        $this->assertNull($context->other);
    }
}
