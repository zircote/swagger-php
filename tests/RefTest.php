<?php

/**
 * @license Apache 2.0
 */

namespace SwaggerTests;

use Swagger\Analysis;
use Swagger\Annotations\Info;
use Swagger\Annotations\Response;
use Swagger\Context;

class RefTest extends SwaggerTestCase
{
    public function testRef()
    {
        $swagger = $this->createSwaggerWithInfo();
        $info = $swagger->ref('#/info');
        $this->assertInstanceOf(Info::class, $info);

        $comment = <<<END
@SWG\Get(
    path="/api/~/endpoint",
    @SWG\Response(response="default", description="A response")
)
END;
        $swagger->merge($this->parseComment($comment));
        $analysis = new Analysis();
        $analysis->addAnnotation($swagger, Context::detect());
        $analysis->process();
        
        $analysis->validate();
        // escape / as ~1
        // escape ~ as ~0
        $response = $swagger->ref('#/paths/~1api~1~0~1endpoint/get/responses/default');
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame('A response', $response->description);
    }
}
