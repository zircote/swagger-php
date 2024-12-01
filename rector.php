<?php

use Rector\CodeQuality\Rector\ClassMethod\ExplicitReturnNullRector;
use Rector\CodeQuality\Rector\For_\ForRepeatedCountToOwnVariableRector;
use Rector\CodeQuality\Rector\If_\CombineIfRector;
use Rector\CodeQuality\Rector\If_\ExplicitBoolCompareRector;
use Rector\CodeQuality\Rector\If_\ShortenElseIfRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\If_\RemoveAlwaysTrueIfConditionRector;
use Rector\DeadCode\Rector\If_\RemoveDeadInstanceOfRector;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromStrictConstructorRector;
use Rector\ValueObject\PhpVersion;

return RectorConfig::configure()
    ->withRules([
        TypedPropertyFromStrictConstructorRector::class
    ])
    ->withSkip([
        CombineIfRector::class,
        ExplicitBoolCompareRector::class,
        ForRepeatedCountToOwnVariableRector::class,
        RemoveAlwaysTrueIfConditionRector::class => [
            __DIR__ . '/src/Processors/ExpandEnums.php',
        ] ,
        RemoveDeadInstanceOfRector::class => [
            __DIR__ . '/src/Processors/ExpandEnums.php',
        ],
        ShortenElseIfRector::class,
    ])
    ->withPreparedSets(true, true)
    ->withPhpVersion(PhpVersion::PHP_74);
