<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

use OpenApi\Rector\AliasSpecImportsRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withConfiguredRule(AliasSpecImportsRector::class, [AliasSpecImportsRector::ALIAS => 'OAS']);
