<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

use OpenApi\Rector\UnnestOpenApiRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withRules([UnnestOpenApiRector::class]);
