<?php

/**
 * @license Apache 2.0
 */

namespace SwaggerTests;

use Swagger\Annotations\Schema;

class HandleReferencesTest extends SwaggerTestCase
{
    public function testRef()
    {
        $this->markTestSkipped();
        /** @var \Swagger\Annotations\OpenApi $openapi */
        $openapi = \Swagger\scan(__DIR__ . '/Fixtures/DynamicReference.php');

        /** @var Schema $schema */
        $schema = $openapi->paths[0]->post->responses[0]->content[0]->schema;
        $props = $schema->properties;

        $this->assertEquals('success', $props[0]->property, 'The @OAS\Schema should contain properties from the extended @OAS\Response');
        $this->assertEquals('data', $props[1]->property, 'The @OAS\Schema should contain properties from the extended @OAS\Response');
        $this->assertEquals('#/components/schemas/Product', $props[1]->ref, 'The @OAS\Schema should contain the original property from the child. This property will override the parent property from @OAS\Response');
        $this->assertEquals('errors', $props[2]->property, 'The @OAS\Schema should contain properties from the extended @OAS\Response');
        $this->assertEquals('token', $props[3]->property, 'The @OAS\Schema should contain properties from the extended @OAS\Response');
        $this->assertEquals('status', $props[4]->property, 'The @OAS\Schema should contain properties from the extended @OAS\Response');
        $this->assertEquals('test', $props[5]->property, 'The @OAS\Schema should contain properties from the extended @OAS\Response');
        $this->assertEquals('string', $props[5]->type, 'The @OAS\Schema should contain properties from the extended @OAS\Response');
        $this->assertEquals('The status of a product', $props[5]->description, 'The @OAS\Schema should contain properties from the extended @OAS\Response');
    }
}
