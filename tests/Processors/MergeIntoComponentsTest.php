<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Processors;

use OpenApi\Analysis;
use OpenApi\Annotations\OpenApi;
use OpenApi\Annotations\Response;
use OpenApi\Context;
use OpenApi\Generator;
use OpenApi\Processors\MergeIntoComponents;
use OpenApi\Tests\OpenApiTestCase;

class MergeIntoComponentsTest extends OpenApiTestCase
{
    public function testProcessor()
    {
        $openapi = new OpenApi([]);
        $response = new Response(['response' => '2xx']);
        $analysis = new Analysis(
            [
                $openapi,
                $response,
            ],
            new Context()
        );
        $this->assertSame(Generator::UNDEFINED, $openapi->components);
        $analysis->process(new MergeIntoComponents());
        $this->assertCount(1, $openapi->components->responses);
        $this->assertSame($response, $openapi->components->responses[0]);
        $this->assertCount(0, $analysis->unmerged()->annotations);
    }
}
