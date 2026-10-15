<?php declare(strict_types=1);

/**
 * @license Apache 2.0
 */

use OpenApi\Rector\SecurityStringToRequirementRector;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withRules([SecurityStringToRequirementRector::class]);
