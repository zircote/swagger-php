<?php

use OpenApi\Builder;
use OpenApi\Builder\Mode;
use OpenApi\Utils\Pipeline;
use SortComponentsAugmenter\SortComponents;

$classLoader = require __DIR__ . '/../../../../vendor/autoload.php';

// register our app namespace...
$classLoader->addPsr4('App\\', __DIR__ . '/app');
// and our custom augmenter
$classLoader->addPsr4('SortComponentsAugmenter\\', __DIR__);

// only the controller and the info: the resolver adds the schemas in the order they are referenced
$result = (new Builder())
    ->setMode(Mode::SPEC)
    ->addSource(__DIR__ . '/app/OpenApi.php')
    ->addSource(__DIR__ . '/app/ProductController.php')
    ->withAugmenters(fn (Pipeline $pipeline): Pipeline => $pipeline->add(new SortComponents()))
    ->build();
// file_put_contents(__DIR__ . '/sort-components.yaml', $result->toYaml());
echo $result->toYaml();
