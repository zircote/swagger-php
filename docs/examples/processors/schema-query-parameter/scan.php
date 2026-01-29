<?php

use OpenApi\Generator;
use OpenApi\Pipeline;
use OpenApi\Processors\BuildPaths;
use SchemaQueryParameterProcessor\SchemaQueryParameter;

$classLoader = require __DIR__ . '/../../../vendor/autoload.php';

// register our app namespace...
$classLoader->addPsr4('App\\', __DIR__ . '/app');
// and our custom processor
$classLoader->addPsr4('SchemaQueryParameterProcessor\\', __DIR__);

$insertMatch = function (array $pipes) {
    foreach ($pipes as $ii => $pipe) {
        if ($pipe instanceof BuildPaths) {
            return $ii;
        }
    }

    return null;
};

$openapi = (new Generator())
    ->withProcessorPipeline(fn (Pipeline $pipeline) => $pipeline->insert(new SchemaQueryParameter(), $insertMatch))
    ->generate([__DIR__ . '/app']);
// file_put_contents(__DIR__ . '/schema-query-parameter.yaml', $openapi->toYaml());
echo $openapi->toYaml();
