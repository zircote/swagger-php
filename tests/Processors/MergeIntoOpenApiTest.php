<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Processors;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Generator;
use OpenApi\Processors\MergeIntoOpenApi;
use OpenApi\Tests\OpenApiTestCase;

final class MergeIntoOpenApiTest extends OpenApiTestCase
{
    public function testProcessor(): void
    {
        $openapi = new OA\OpenApi(['_context' => $this->getContext()]);
        $info = new OA\Info(['_context' => $this->getContext()]);
        $analysis = new Analysis(
            [
                $openapi,
                $info,
            ],
            $this->getContext()
        );
        $this->assertSame($openapi, $analysis->openapi);
        $this->assertSame(Generator::UNDEFINED, $openapi->info);
        $analysis->process([new MergeIntoOpenApi()]);

        $this->assertSame($openapi, $analysis->openapi);
        $this->assertSame($info, $openapi->info);
        $this->assertCount(0, $analysis->unmerged()->annotations);
    }
}
