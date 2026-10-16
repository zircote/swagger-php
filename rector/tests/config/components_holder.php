<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

use OpenApi\Rector\ComponentsHolderRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withRules([ComponentsHolderRector::class]);
