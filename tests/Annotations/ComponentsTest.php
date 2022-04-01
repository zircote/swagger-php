<?php declare(strict_types=1);

namespace OpenApi\Tests\Annotations;

use OpenApi\Annotations\Examples;
use OpenApi\Attributes\Components;
use OpenApi\Attributes\Schema;
use OpenApi\Tests\OpenApiTestCase;

class ComponentsTest extends OpenApiTestCase
{
    public function testRef()
    {
        $this->assertEquals('#/components/schemas/foo', Components::ref('foo'));
        $this->assertEquals('#/components/schemas/bar', Components::ref(new Schema(null, 'bar')));
        $this->assertEquals('#/components/examples/xx', Components::ref(new Examples(['example' => 'xx'])));
    }
}
