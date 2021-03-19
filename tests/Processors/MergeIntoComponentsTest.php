<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Processors;

use OpenApi\Analysis;
use OpenApi\Annotations\OpenApi;
use OpenApi\Annotations\Response;
use OpenApi\Processors\MergeIntoComponents;
use OpenApi\Tests\OpenApiTestCase;
use const OpenApi\UNDEFINED;

class MergeIntoComponentsTest extends OpenApiTestCase
{
    public function testProcessor()
    {
        $logger = $this->getLogger();

        $openapi = new OpenApi([], $logger);
        $response = new Response(['response' => '2xx'], $logger);
        $analysis = new Analysis([
            $openapi,
            $response,
        ], null, $logger);
        $this->assertSame(UNDEFINED, $openapi->components);
        $analysis->process(new MergeIntoComponents($logger));
        $this->assertCount(1, $openapi->components->responses);
        $this->assertSame($response, $openapi->components->responses[0]);
        $this->assertCount(0, $analysis->unmerged()->annotations);
    }
}
