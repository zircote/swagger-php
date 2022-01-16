<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Processors;

use OpenApi\Analysis;
use OpenApi\Annotations\Parameter;
use OpenApi\Annotations\Response;
use OpenApi\Generator;
use OpenApi\Processors\MergeJsonContent;
use OpenApi\Tests\OpenApiTestCase;

class MergeJsonContentTest extends OpenApiTestCase
{
    public function testJsonContent(): void
    {
        $comment = <<<END
            @OA\Response(response=200,
                @OA\JsonContent(type="array",
                    @OA\Items(ref="#/components/schemas/repository")
                )
            )
END;
        $analysis = new Analysis($this->annotationsFromDocBlockParser($comment), $this->getContext());
        $this->assertCount(3, $analysis->annotations);
        /** @var Response $response */
        $response = $analysis->getAnnotationsOfType(Response::class)[0];
        $this->assertSame(Generator::UNDEFINED, $response->content);
        $this->assertCount(1, $response->_unmerged);
        $analysis->process([new MergeJsonContent()]);

        $this->assertCount(1, $response->content);
        $this->assertCount(0, $response->_unmerged);
        $json = json_decode(json_encode($response), true);
        $this->assertSame('#/components/schemas/repository', $json['content']['application/json']['schema']['items']['$ref']);
    }

    public function testMultipleMediaTypes(): void
    {
        $comment = <<<END
            @OA\Response(response=200,
                @OA\MediaType(mediaType="image/png"),
                @OA\JsonContent(type="array",
                    @OA\Items(ref="#/components/schemas/repository")
                )
            )
END;
        $analysis = new Analysis($this->annotationsFromDocBlockParser($comment), $this->getContext());
        /** @var Response $response */
        $response = $analysis->getAnnotationsOfType(Response::class)[0];
        $this->assertCount(1, $response->content);
        $analysis->process([new MergeJsonContent()]);

        $this->assertCount(2, $response->content);
    }

    public function testParameter(): void
    {
        $comment = <<<END
            @OA\Parameter(name="filter",in="query", @OA\JsonContent(
                @OA\Property(property="type", type="string"),
                @OA\Property(property="color", type="string")
            ))
END;
        $analysis = new Analysis($this->annotationsFromDocBlockParser($comment), $this->getContext());
        $this->assertCount(4, $analysis->annotations);
        /** @var Parameter $parameter */
        $parameter = $analysis->getAnnotationsOfType(Parameter::class)[0];
        $this->assertSame(Generator::UNDEFINED, $parameter->content);
        $this->assertCount(1, $parameter->_unmerged);
        $analysis->process([new MergeJsonContent()]);

        $this->assertCount(1, $parameter->content);
        $this->assertCount(0, $parameter->_unmerged);
        $json = json_decode(json_encode($parameter), true);
        $this->assertSame('query', $json['in']);
        $this->assertSame('application/json', array_keys($json['content'])[0]);
        $this->assertArrayNotHasKey('mediaType', $json['content']['application/json']);
    }

    public function testNoParent(): void
    {
        $this->assertOpenApiLogEntryContains('Unexpected @OA\JsonContent() must be nested');
        $comment = <<<END
            @OA\JsonContent(type="array",
                @OA\Items(ref="#/components/schemas/repository")
            )
END;
        $analysis = new Analysis($this->annotationsFromDocBlockParser($comment), $this->getContext());
        $analysis->process([new MergeJsonContent()]);
    }

    public function testInvalidParent(): void
    {
        $this->assertOpenApiLogEntryContains('Unexpected @OA\JsonContent() in @OA\Property() in');
        $comment = <<<END
            @OA\Property(
                @OA\JsonContent(type="array",
                    @OA\Items(ref="#/components/schemas/repository")
                )
            )
END;
        $analysis = new Analysis($this->annotationsFromDocBlockParser($comment), $this->getContext());
        $analysis->process([new MergeJsonContent()]);
    }
}
