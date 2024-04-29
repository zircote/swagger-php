<?php

use Rector\CodeQuality\Rector\If_\ExplicitBoolCompareRector;
use Rector\Config\RectorConfig;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromStrictConstructorRector;
use Rector\ValueObject\PhpVersion;

return RectorConfig::configure()
    ->withRules([
        TypedPropertyFromStrictConstructorRector::class
    ])
    ->withSkip([
        ExplicitBoolCompareRector::class,
    ])
    ->withPreparedSets(true, true)
    ->withPhpVersion(PhpVersion::PHP_74)
;
