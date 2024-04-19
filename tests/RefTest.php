<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Generator;

class RefTest extends OpenApiTestCase
{
    public function testRef(): void
    {
        $openapi = $this->createOpenApiWithInfo();
        $info = $openapi->ref('#/info');
        $this->assertInstanceOf(OA\Info::class, $info);

        $comment = <<<END
@OA\Get(
    path="/api/~/endpoint",
    @OA\Response(response="default", description="A response")
)
END;
        $openapi->merge($this->annotationsFromDocBlockParser($comment));
        $analysis = new Analysis([], $this->getContext());
        $analysis->addAnnotation($openapi, $this->getContext());
        (new Generator())->getProcessor()->process($analysis);

        $analysis->validate();
        // escape / as ~1
        // escape ~ as ~0
        $response = $openapi->ref('#/paths/~1api~1~0~1endpoint/get/responses/default');
        $this->assertInstanceOf(OA\Response::class, $response);
        $this->assertSame('A response', $response->description);
    }
}
