<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Processors;

use OpenApi\Processors\AugmentRefs;
use OpenApi\Processors\AugmentRequestBody;
use OpenApi\Processors\BuildPaths;
use OpenApi\Processors\Concerns\DocblockTrait;
use OpenApi\Processors\MergeIntoComponents;
use OpenApi\Processors\MergeIntoOpenApi;
use OpenApi\Tests\OpenApiTestCase;

class AugmentRefsTest extends OpenApiTestCase
{
    use DocblockTrait;

    public function testAugmentRefsForRequestBody(): void
    {
        $analysis = $this->analysisFromFixtures(['Request.php']);
        $analysis->process([
            // create openapi->components
            new MergeIntoOpenApi(),
            // Merge standalone Scheme's into openapi->components
            new MergeIntoComponents(),
            new BuildPaths(),
            new AugmentRequestBody(),
        ]);

        $this->assertSame($analysis->openapi->paths[0]->post->requestBody->ref, 'OpenApi\Tests\Fixtures\Request');

        $analysis->process([
            new AugmentRefs(),
        ]);

        $this->assertSame($analysis->openapi->paths[0]->post->requestBody->ref, '#/components/requestBodies/Request');
    }
}
