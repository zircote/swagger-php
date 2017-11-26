<?php

/**
 * @license Apache 2.0
 */

namespace SwaggerTests;

use Swagger\Analysis;
use Swagger\Annotations\Info;
use Swagger\Annotations\Response;
use Swagger\Context;

class DynamicReferenceTest extends SwaggerTestCase
{
    public function testRef()
    {
        $swagger = $this->createSwaggerWithInfo();
        $info = $swagger->ref('#/info');
        $this->assertInstanceOf(Info::class, $info);

        $comment = <<<END
@SWG\Get(
    path="/api/endpoint",
    @SWG\Response(response="default", description="A response")
)
END;
        $swagger->merge($this->parseComment($comment));
        $analysis = new Analysis();
        $analysis->addAnnotation($swagger, Context::detect());
        $analysis->process();

        $analysis->validate();
        $response = $swagger->ref('#/paths/%2fapi%2fendpoint/get/responses/default');
        $this->assertInstanceOf(Response::class, $response);
        $this->assertSame('A response', $response->description);
    }
}
