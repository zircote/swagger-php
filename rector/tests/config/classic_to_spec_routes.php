<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

use OpenApi\Rector\ComponentsHolderRector;
use Rector\Config\RectorConfig;

// the set, configured the way the migration guide shows
return RectorConfig::configure()
    ->withSets([__DIR__ . '/../../set/classic-to-spec.php'])
    ->withConfiguredRule(ComponentsHolderRector::class, [
        ComponentsHolderRector::ROUTE_ATTRIBUTES => ['Symfony\Component\Routing\Attribute\Route'],
    ]);
