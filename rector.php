<?php

use Rector\CodingStyle\Rector\ClassMethod\NewlineBeforeNewAssignSetRector;
use Rector\CodingStyle\Rector\Encapsed\EncapsedStringsToSprintfRector;
use Rector\CodingStyle\Rector\Encapsed\WrapEncapsedVariableInCurlyBracesRector;
use Rector\CodingStyle\Rector\If_\NullableCompareToNullRector;
use Rector\CodeQuality\Rector\BooleanAnd\RepeatedAndNotEqualToNotInArrayRector;
use Rector\CodingStyle\Rector\Stmt\NewlineAfterStatementRector;
use Rector\Config\RectorConfig;
use Rector\Php55\Rector\String_\StringClassNameToClassConstantRector;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Php81\Rector\Array_\ArrayToFirstClassCallableRector;
use Rector\Php81\Rector\Property\ReadOnlyPropertyRector;
use Rector\ValueObject\PhpVersion;

return RectorConfig::configure()
    ->withSkipPath(__DIR__ . '/tests/Fixtures')
    ->withSkip([
        NewlineBeforeNewAssignSetRector::class,
        ReadOnlyPropertyRector::class,
        NewlineAfterStatementRector::class,
        StringClassNameToClassConstantRector::class => [
            __DIR__ . '/src/Analysers/DocBlockParser.php',
            __DIR__ . '/tests/AnalysisTest.php',
            __DIR__ . '/tests/ContextTest.php',
            // provider keys are names the scanner reads out of a file, not classes this
            // test loads — `::class` would imply a link that is not there
            __DIR__ . '/tests/Utils/TokenScannerTest.php',
        ],
        ClassPropertyAssignToConstructorPromotionRector::class,
        ArrayToFirstClassCallableRector::class => [
            __DIR__ . '/tests/Analysers/ComposerAutoloaderScannerTest.php',
        ],
        // runs for every attribute constructed; `in_array()` there is measurably slower
        RepeatedAndNotEqualToNotInArrayRector::class => [
            __DIR__ . '/src/Spec/AbstractAttribute.php',
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
