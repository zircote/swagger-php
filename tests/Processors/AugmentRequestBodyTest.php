<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Processors;

use OpenApi\Generator;
use OpenApi\Processors\AugmentRequestBody;
use OpenApi\Processors\MergeIntoComponents;
use OpenApi\Processors\MergeIntoOpenApi;
use OpenApi\Tests\OpenApiTestCase;

final class AugmentRequestBodyTest extends OpenApiTestCase
{
    public function testAugmentSchemas(): void
    {
        $analysis = $this->analysisFromFixtures([
            'Request.php',
        ], $this->processorPipeline([
            // create openapi->components
            new MergeIntoOpenApi(),
            // Merge standalone Scheme's into openapi->components
            new MergeIntoComponents(),
        ]));

        $this->assertCount(1, $analysis->openapi->components->requestBodies);
        $request = $analysis->openapi->components->requestBodies[0];
        $this->assertSame(Generator::UNDEFINED, $request->request, 'Sanity check. No request was defined');

        $this->processorPipeline([new AugmentRequestBody()])->process($analysis);

        $this->assertSame('Request', $request->request, '@OA\RequestBody()->request based on classname');
    }
}
