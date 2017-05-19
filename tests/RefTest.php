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
        $openapi = $this->createSwaggerWithInfo();
        $info = $openapi->ref('#/info');
        $this->assertInstanceOf(Info::class, $info);

        $comment = <<<END
@SWG\Get(
    path="/api/endpoint",
    @SWG\Response(response="default", description="A response")
)
END;
        $openapi->merge($this->parseComment($comment));
        $analysis = new Analysis();
        $analysis->addAnnotation($openapi, Context::detect());
        $analysis->process();
        
        $analysis->validate();
        $response = $openapi->ref('#/paths/%2fapi%2fendpoint/get/responses/default');
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame('A response', $response->description);
    }
}
