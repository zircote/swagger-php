<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Annotations;

use OpenApi\Annotations as OA;
use OpenApi\Tests\OpenApiTestCase;

class ComponentsTest extends OpenApiTestCase
{
    public function testRef(): void
    {
        $this->assertEquals('#/components/schemas/foo', OA\Components::ref('foo'));
        $this->assertEquals('#/components/schemas/bar', OA\Components::ref(new OA\Schema(['ref' => null, 'schema' => 'bar', '_context' => $this->getContext()])));
        $this->assertEquals('#/components/examples/xx', OA\Components::ref(new OA\Examples(['example' => 'xx', '_context' => $this->getContext()])));
    }

    public function testRefEncode(): void
    {
        $this->assertSame('#/paths/~1blogs~1{blog_id}~1new~0posts', '#/paths/' . OA\Components::refEncode('/blogs/{blog_id}/new~posts'));
    }

    public function testRefDecode(): void
    {
        $this->assertSame('/blogs/{blog_id}/new~posts', OA\Components::refDecode('~1blogs~1{blog_id}~1new~0posts'));
    }
}
