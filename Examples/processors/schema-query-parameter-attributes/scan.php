<?php

use OpenApi\Generator;
use OpenApi\Processors\BuildPaths;
use SchemaQueryParameterProcessor\SchemaQueryParameter;

$classLoader = require __DIR__ . '/../../../vendor/autoload.php';

// register our app namespace...
$classLoader->addPsr4('App\\', __DIR__ . '/app');
// and our custom processor
$classLoader->addPsr4('SchemaQueryParameterProcessor\\', __DIR__);

$openapiGenerator = new Generator();
$processors = [];
foreach ($openapiGenerator->getProcessors() as $processor) {
    $processors[] = $processor;
    if ($processor instanceof BuildPaths) {
        $processors[] = new SchemaQueryParameter();
    }
}

$openapi =  $openapiGenerator
    ->setProcessors($processors)
    ->generate([__DIR__ . '/app']);

file_put_contents(__DIR__ . '/schema-query-parameter.yaml', $openapi->toYaml());

echo $openapi->toYaml();
