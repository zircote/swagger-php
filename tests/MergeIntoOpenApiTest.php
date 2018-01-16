<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace SwaggerTests;

use Swagger\Annotations\Info;
use Swagger\Annotations\OpenApi;
use Swagger\Analysis;
use Swagger\Processors\MergeIntoOpenApi;

class MergeIntoOpenApiTest extends SwaggerTestCase
{
    public function testProcessor()
    {
        $openapi = new OpenApi([]);
        $info = new Info([]);
        $analysis = new Analysis([
            $openapi,
            $info
        ]);
        $this->assertSame($openapi, $analysis->openapi);
        $this->assertNull($openapi->info);
        $analysis->process(new MergeIntoOpenApi());
        $this->assertSame($openapi, $analysis->openapi);
        $this->assertSame($info, $openapi->info);
        $this->assertCount(0, $analysis->unmerged()->annotations);
    }
}
