<?php declare(strict_types=1);

namespace OpenApi\Tests\Annotations;

use OpenApi\Annotations\Examples;
use OpenApi\Annotations\Components;
use OpenApi\Annotations\Schema;
use OpenApi\Tests\OpenApiTestCase;

class ComponentsTest extends OpenApiTestCase
{
    public function testRef()
    {
        $this->assertEquals('#/components/schemas/foo', Components::ref('foo'));
        $this->assertEquals('#/components/schemas/bar', Components::ref(new Schema(['ref' => null, 'schema' => 'bar'])));
        $this->assertEquals('#/components/examples/xx', Components::ref(new Examples(['example' => 'xx'])));
    }
}
