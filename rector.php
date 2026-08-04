<?php

use Rector\CodeQuality\Rector\Class_\CompleteDynamicPropertiesRector;
use Rector\CodeQuality\Rector\For_\ForRepeatedCountToOwnVariableRector;
use Rector\CodingStyle\Rector\ClassMethod\NewlineBeforeNewAssignSetRector;
use Rector\CodingStyle\Rector\Encapsed\EncapsedStringsToSprintfRector;
use Rector\CodingStyle\Rector\Encapsed\WrapEncapsedVariableInCurlyBracesRector;
use Rector\CodingStyle\Rector\If_\NullableCompareToNullRector;
use Rector\CodingStyle\Rector\Stmt\NewlineAfterStatementRector;
use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\If_\RemoveAlwaysTrueIfConditionRector;
use Rector\DeadCode\Rector\If_\RemoveDeadInstanceOfRector;
use Rector\Php55\Rector\String_\StringClassNameToClassConstantRector;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Php81\Rector\Array_\ArrayToFirstClassCallableRector;
use Rector\Php81\Rector\Property\ReadOnlyPropertyRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ParamTypeByMethodCallTypeRector;
use Rector\ValueObject\PhpVersion;

return RectorConfig::configure()
    ->withSkipPath(__DIR__ . '/tests/Fixtures')
    ->withSkip([
        NewlineBeforeNewAssignSetRector::class,
        ForRepeatedCountToOwnVariableRector::class,
        ReadOnlyPropertyRector::class,
        RemoveAlwaysTrueIfConditionRector::class => [
            __DIR__ . '/src/Processors/ExpandEnums.php',
        ],
        RemoveDeadInstanceOfRector::class => [
            __DIR__ . '/src/Processors/ExpandEnums.php',
        ],
        NewlineAfterStatementRector::class,
        StringClassNameToClassConstantRector::class => [
            __DIR__ . '/src/Analysers/DocBlockParser.php',
            __DIR__ . '/src/Analysers/TypeResolverTrait.php',
            __DIR__ . '/tests/Analysers/ComposerAutoloaderScannerTest.php',
            __DIR__ . '/tests/Analysers/TokenScannerTest.php',
            __DIR__ . '/tests/AnalysisTest.php',
            __DIR__ . '/tests/ContextTest.php',
            __DIR__ . '/tests/Utils/TokenScannerTest.php',
        ],
        ParamTypeByMethodCallTypeRector::class => [
            __DIR__ . '/src/Serializer.php',
        ],
        ClassPropertyAssignToConstructorPromotionRector::class,
        CompleteDynamicPropertiesRector::class => [
            __DIR__ . '/src/Annotations/AbstractAnnotation.php',
        ],
        ArrayToFirstClassCallableRector::class => [
            __DIR__ . '/tests/Analysers/ComposerAutoloaderScannerTest.php',
        ],
    ])
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        phpunitCodeQuality: true,
    )
    ->withAttributesSets(phpunit: true)
    ->withPhpVersion(PhpVersion::PHP_82)
    ->withPhpSets();
