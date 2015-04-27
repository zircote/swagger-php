<?php

/**
 * @license Apache 2.0
 */

namespace SwaggerTests;

use Swagger\Annotations\Swagger;
use Swagger\Processors\BuildPaths;
use Swagger\Annotations\Path;
use Swagger\Annotations\Get;
use Swagger\Annotations\Post;

class BuildPathsTest extends SwaggerTestCase
{

    public function testMergePathsWithSamePath()
    {
        $swagger = new Swagger([]);
        $swagger->paths = [
            new Path(['path' => '/comments']),
            new Path(['path' => '/comments'])
        ];
        $processor = new BuildPaths();
        $processor($swagger);
        $this->assertCount(1, $swagger->paths);
        $this->assertSame('/comments', $swagger->paths[0]->path);
    }

    public function testMergeOperationsWithSamePath()
    {
        $swagger = new Swagger([]);
        $swagger->_unmerged = [
            new Get(['path' => '/comments']),
            new Post(['path' => '/comments'])
        ];
        $processor = new BuildPaths();
        $processor($swagger);
        $this->assertCount(1, $swagger->paths);
        $path = $swagger->paths[0];
        $this->assertSame('/comments', $path->path);
        $this->assertInstanceOf('\Swagger\Annotations\Path', $path);
        $this->assertInstanceOf('\Swagger\Annotations\Get', $path->get);
        $this->assertInstanceOf('\Swagger\Annotations\Post', $path->post);
        $this->assertNull($path->put);
    }
}
