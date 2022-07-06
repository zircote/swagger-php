<?php declare(strict_types=1);

namespace OpenApi\Tests\Analysers;

use OpenApi\Analysers\AttributeAnnotationFactory;
use OpenApi\Tests\Fixtures\UsingAttributes;
use OpenApi\Tests\OpenApiTestCase;

/**
 * @requires PHP 8.1
 */
class AttributeAnnotationFactoryTest extends OpenApiTestCase
{
    public function testReturnedAnnotationsCout()
    {
        $rc = new \ReflectionClass(UsingAttributes::class);

        $annotations = (new AttributeAnnotationFactory())->build($rc, $this->getContext());
        $this->assertCount(1, $annotations);
    }
}
