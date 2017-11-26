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
        $swagger = \Swagger\scan(__DIR__ . '/Fixtures/DynamicReference.php');

        /** @var Schema $schema */
        $schema = $swagger->paths[0]->post->responses[0]->schema;
        $props = $schema->properties;

        $this->assertEquals('success', $props[0]->property, 'The @SWG\Schema should contain properties from the extended @SWG\Response');
        $this->assertEquals('#/definitions/Product', $props[1]->ref, 'The @SWG\Schema should contain the original property from the child. This property will override the parent property from @SWG\Response');
        $this->assertEquals('errors', $props[2]->property, 'The @SWG\Schema should contain properties from the extended @SWG\Response');
        $this->assertEquals('token', $props[3]->property, 'The @SWG\Schema should contain properties from the extended @SWG\Response');
    }
}
