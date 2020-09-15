<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Processors;

use OpenApi\Analysis;
use OpenApi\Annotations\Info;
use OpenApi\Annotations\OpenApi;
use OpenApi\Processors\MergeIntoOpenApi;
use OpenApi\Tests\OpenApiTestCase;
use const OpenApi\UNDEFINED;

class MergeIntoOpenApiTest extends OpenApiTestCase
{
    public function testProcessor()
    {
        $logger = $this->trackingLogger();

        $openapi = new OpenApi([], $logger);
        $info = new Info([], $logger);
        $analysis = new Analysis([
            $openapi,
            $info,
        ], null, $logger);
        $this->assertSame($openapi, $analysis->openapi);
        $this->assertSame(UNDEFINED, $openapi->info);
        $analysis->process(new MergeIntoOpenApi($logger));
        $this->assertSame($openapi, $analysis->openapi);
        $this->assertSame($info, $openapi->info);
        $this->assertCount(0, $analysis->unmerged()->annotations);
    }
}
