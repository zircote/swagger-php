<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace SwaggerTests;

class AugmentParameterTest extends SwaggerTestCase
{
    public function testAugmentParameter()
    {
        $openapi = \Swagger\scan(__DIR__.'/Fixtures/UsingRefs.php');
        $this->assertCount(1, $openapi->components->parameters, 'Swagger contains 1 reusable parameter specification');
        $this->assertEquals('ItemName', $openapi->components->parameters[0]->parameter, 'When no @OAS\Parameter()->parameter is specified, use @OAS\Parameter()->name');
    }
}
