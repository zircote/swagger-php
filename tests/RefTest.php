<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;

class RefTest extends OpenApiTestCase
{
    public function testRef(): void
    {
        $openapi = $this->createOpenApiWithInfo();
        $info = $openapi->ref('#/info');
        $this->assertInstanceOf(OA\Info::class, $info);

        $comment = <<<END
@OA\Post(
    path="/api/~/endpoint",
    @OA\Response(
        response="default",
        description="A response",
        @OA\JsonContent(ref="#/components/schemas/String/x-custom-key")
    )
)

@OA\Schema(
     schema="String",
     @OA\Property(property="value", type="string"),
     x={
        "custom-key": @OA\Schema(
            @OA\Property(property="value", type="string"),
        )
    }
)
END;
        $openapi->merge($this->annotationsFromDocBlockParser($comment));
        $analysis = new Analysis([], $this->getContext());
        $analysis->addAnnotation($openapi, $this->getContext());
        $this->processorPipeline()->process($analysis);

        $analysis->validate();
        // escape / as ~1
        // escape ~ as ~0
        $response = $openapi->ref('#/paths/~1api~1~0~1endpoint/post/responses/default');
        $this->assertInstanceOf(OA\Response::class, $response);
        $this->assertSame('A response', $response->description);

        // ref x value
        $schema = $openapi->ref('#/components/schemas/String/x-custom-key');
        $this->assertInstanceOf(OA\Schema::class, $schema);

        // ref x with deep
        $property = $openapi->ref('#/components/schemas/String/x-custom-key/properties/value');
        $this->assertInstanceOf(OA\Property::class, $property);
        $this->assertSame('string', $property->type);
    }
}
