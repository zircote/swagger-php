<?php

use OpenApi\Builder;
use OpenApi\Builder\Mode;
use OpenApi\Utils\Pipeline;
use SchemaQueryParameterAugmenter\SchemaQueryParameter;

$classLoader = require __DIR__ . '/../../../../vendor/autoload.php';

// register our app namespace...
$classLoader->addPsr4('App\\', __DIR__ . '/app');
// and our custom augmenter
$classLoader->addPsr4('SchemaQueryParameterAugmenter\\', __DIR__);

$result = (new Builder())
    ->setMode(Mode::SPEC)
    ->addSource(__DIR__ . '/app')
    ->withAugmenters(fn (Pipeline $pipeline): Pipeline => $pipeline->add(new SchemaQueryParameter()))
    ->build();
// file_put_contents(__DIR__ . '/schema-query-parameter.yaml', $result->toYaml());
echo $result->toYaml();
