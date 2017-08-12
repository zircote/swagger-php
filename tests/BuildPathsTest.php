<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

namespace SwaggerTests;

use Swagger\Analysis;
use Swagger\Annotations\Get;
use Swagger\Annotations\Path;
use Swagger\Annotations\Post;
use Swagger\Annotations\OpenApi;
use Swagger\Processors\BuildPaths;
use Swagger\Processors\MergeIntoOpenApi;

class BuildPathsTest extends SwaggerTestCase
{
    public function testMergePathsWithSamePath()
    {
        $openapi = new OpenApi([]);
        $openapi->paths = [
            new Path(['path' => '/comments']),
            new Path(['path' => '/comments'])
        ];
        $analysis = new Analysis([$openapi]);
        $analysis->openapi = $openapi;
        $analysis->process(new BuildPaths());
        $this->assertCount(1, $openapi->paths);
        $this->assertSame('/comments', $openapi->paths[0]->path);
    }

    public function testMergeOperationsWithSamePath()
    {
        $openapi = new OpenApi([]);
        $analysis = new Analysis([
            $openapi,
            new Get(['path' => '/comments']),
            new Post(['path' => '/comments'])
        ]);
        $analysis->process(new MergeIntoOpenApi());
        $analysis->process(new BuildPaths());
        $this->assertCount(1, $openapi->paths);
        $path = $openapi->paths[0];
        $this->assertSame('/comments', $path->path);
        $this->assertInstanceOf('\Swagger\Annotations\Path', $path);
        $this->assertInstanceOf('\Swagger\Annotations\Get', $path->get);
        $this->assertInstanceOf('\Swagger\Annotations\Post', $path->post);
        $this->assertNull($path->put);
    }
}
