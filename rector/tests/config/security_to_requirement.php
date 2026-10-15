<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

use OpenApi\Rector\SecurityToRequirementRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withRules([SecurityToRequirementRector::class]);
