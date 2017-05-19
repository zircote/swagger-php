<?php

/**
 * @license Apache 2.0
 */

namespace SwaggerTests;

use Swagger\Annotations\Info;
use Swagger\Annotations\OpenApi;
use Swagger\Analysis;
use Swagger\Processors\MergeIntoOpenApi;

class MergeIntoSwaggerTest extends SwaggerTestCase
{
    public function testProcessor()
    {
        $openapi = new OpenApi([]);
        $info = new Info([]);
        $analysis = new Analysis([
            $openapi,
            $info
        ]);
        $this->assertNull($analysis->openapi);
        $this->assertNull($openapi->info);
        $analysis->process(new MergeIntoOpenApi());
        $this->assertInstanceOf('Swagger\Annotations\OpenApi', $analysis->openapi);
        $this->assertSame($info, $openapi->info);
        $this->assertCount(0, $analysis->unmerged()->annotations);
    }
}
