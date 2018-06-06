<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace SwaggerTests;

use Swagger\Analysis;
use Swagger\Annotations\OpenApi;
use Swagger\Annotations\Response;
use Swagger\Processors\MergeIntoComponents;

class MergeIntoComponentsTest extends SwaggerTestCase
{
    public function testProcessor()
    {
        $openapi = new OpenApi([]);
        $response = new Response(['response' => '2xx']);
        $analysis = new Analysis([
            $openapi,
            $response,
        ]);
        $this->assertNull($openapi->components);
        $analysis->process(new MergeIntoComponents());
        $this->assertCount(1, $openapi->components->responses);
        $this->assertSame($response, $openapi->components->responses[0]);
        $this->assertCount(0, $analysis->unmerged()->annotations);
    }
}
