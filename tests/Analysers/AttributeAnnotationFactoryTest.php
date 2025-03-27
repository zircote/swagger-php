<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Analysers;

use OpenApi\Analysers\AttributeAnnotationFactory;
use OpenApi\Generator;
use OpenApi\Tests\Fixtures\UsingAttributes;
use OpenApi\Tests\Fixtures\InvalidPropertyAttribute;
use OpenApi\Tests\OpenApiTestCase;

/**
 * @requires PHP 8.1
 */
class AttributeAnnotationFactoryTest extends OpenApiTestCase
{
    protected function getFactory(?array $config = null): AttributeAnnotationFactory
    {
        $generator = new Generator();
        if (null !== $config) {
            $generator->setConfig($config);
        }

        return (new AttributeAnnotationFactory())
            ->setGenerator($generator);
    }

    public function testReturnedAnnotationsCount(): void
    {
        $rc = new \ReflectionClass(UsingAttributes::class);

        $annotations = $this->getFactory()->build($rc, $this->getContext());
        $this->assertCount(1, $annotations);
    }

    public function testErrorOnInvalidAttribute(): void
    {
        $instance = new InvalidPropertyAttribute();
        $rm = new \ReflectionMethod($instance, 'post');

        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('OpenApi\Attributes\Property::__construct(): Argument #8 ($required) must be of type ?array');

        $this->getFactory()->build($rm, $this->getContext());
    }

    public function testIgnoreOtherAttributes(): void
    {
        $rc = new \ReflectionClass(UsingAttributes::class);

        $this->getFactory()->build($rc, $context = $this->getContext());
        $this->assertIsArray($context->other);
        $this->assertCount(1, $context->other);

        $this->getFactory(['generator' => ['ignoreOtherAttributes' => true]])->build($rc, $context = $this->getContext());
        $this->assertNull($context->other);
    }
}
