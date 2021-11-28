<?php

use OpenApi\Generator;
use OpenApi\Processors\BuildPaths;
use SchemaQueryParameterProcessor\SchemaQueryParameter;

require __DIR__ . '/../../../vendor/autoload.php';
// also load our custom processor...
require __DIR__ . '/SchemaQueryParameter.php';

$generator = new Generator();

// merge our custom processor
$processors = [];
foreach ($generator->getProcessors() as $processor) {
    $processors[] = $processor;
    if ($processor instanceof BuildPaths) {
        $processors[] = new SchemaQueryParameter();
    }
}

$options = [
    'processors' => $processors,
];

$openapi = $generator
    ->setProcessors($processors)
    ->generate([__DIR__ . '/app']);
//file_put_contents(__DIR__ . '/schema-query-parameter.yaml', $openapi->toYaml());
echo $openapi->toYaml();