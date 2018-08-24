<?php

/**
 * @license Apache 2.0
 */

namespace OpenApiTests;

use OpenApi\Annotations\Schema;

class HandleReferencesTest extends OpenApiTestCase
{
    public function testRef()
    {
        $this->markTestSkipped();
        /** @var \OpenApi\Annotations\OpenApi $openapi */
        $openapi = \OpenApi\scan(__DIR__ . '/Fixtures/DynamicReference.php');

        /** @var Schema $schema */
        $schema = $openapi->paths[0]->post->responses[0]->content[0]->schema;
        $props = $schema->properties;

        $this->assertEquals('success', $props[0]->property, 'The @OA\Schema should contain properties from the extended @OA\Response');
        $this->assertEquals('data', $props[1]->property, 'The @OA\Schema should contain properties from the extended @OA\Response');
        $this->assertEquals('#/components/schemas/Product', $props[1]->ref, 'The @OA\Schema should contain the original property from the child. This property will override the parent property from @OA\Response');
        $this->assertEquals('errors', $props[2]->property, 'The @OA\Schema should contain properties from the extended @OA\Response');
        $this->assertEquals('token', $props[3]->property, 'The @OA\Schema should contain properties from the extended @OA\Response');
        $this->assertEquals('status', $props[4]->property, 'The @OA\Schema should contain properties from the extended @OA\Response');
        $this->assertEquals('test', $props[5]->property, 'The @OA\Schema should contain properties from the extended @OA\Response');
        $this->assertEquals('string', $props[5]->type, 'The @OA\Schema should contain properties from the extended @OA\Response');
        $this->assertEquals('The status of a product', $props[5]->description, 'The @OA\Schema should contain properties from the extended @OA\Response');
    }
}
