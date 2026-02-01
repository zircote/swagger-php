<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace OpenApi\Tests\Processors;

use OpenApi\Analysis;
use OpenApi\Annotations as OA;
use OpenApi\Generator;
use OpenApi\Processors\BuildPaths;
use OpenApi\Processors\MergeIntoOpenApi;
use OpenApi\Tests\OpenApiTestCase;

final class BuildPathsTest extends OpenApiTestCase
{
    public function testMergePathsWithSamePath(): void
    {
        $openapi = new OA\OpenApi(['_context' => $this->getContext()]);
        $openapi->paths = [
            new OA\PathItem(['path' => '/comments', '_context' => $this->getContext()]),
            new OA\PathItem(['path' => '/comments', '_context' => $this->getContext()]),
        ];
        $analysis = new Analysis([$openapi], $this->getContext());
        $analysis->openapi = $openapi;
        $analysis->process([new BuildPaths()]);

        $this->assertCount(1, $openapi->paths);
        $this->assertSame('/comments', $openapi->paths[0]->path);
    }

    public function testMergeOperationsWithSamePath(): void
    {
        $openapi = new OA\OpenApi(['_context' => $this->getContext()]);
        $analysis = new Analysis(
            [
                $openapi,
                new OA\Get(['path' => '/comments', '_context' => $this->getContext()]),
                new OA\Post(['path' => '/comments', '_context' => $this->getContext()]),
            ],
            $this->getContext()
        );
        $analysis->process([
            new MergeIntoOpenApi(),
            new BuildPaths(),
        ]);
        $this->assertCount(1, $openapi->paths);
        $path = $openapi->paths[0];
        $this->assertSame('/comments', $path->path);
        $this->assertInstanceOf(OA\PathItem::class, $path);
        $this->assertInstanceOf(OA\Get::class, $path->get);
        $this->assertInstanceOf(OA\Post::class, $path->post);
        $this->assertSame(Generator::UNDEFINED, $path->put);
    }
}
