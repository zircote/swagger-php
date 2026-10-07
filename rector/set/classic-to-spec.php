<?php declare(strict_types=1);

/*
 * The `OpenApi\Attributes` -> `OpenApi\Spec` migration.
 *
 *     vendor/bin/rector process src \
 *         --config vendor/zircote/swagger-php/rector/set/classic-to-spec.php --dry-run
 *
 * What it changes, what it leaves for you, and the generator code it cannot see:
 * https://zircote.github.io/swagger-php/guide/migrating-to-spec
 *
 * This set follows the library's version, carries no backwards-compatibility promise of its
 * own, and goes with classic at 8.0.
 */

use OpenApi\Rector\AliasSpecImportsRector;
use OpenApi\Rector\MoveSchemaKeywordsRector;
use OpenApi\Rector\NamePositionalArgumentsRector;
use OpenApi\Rector\TypeClassStringToRefRector;
use OpenApi\Rector\UnnestOpenApiRector;
use OpenApi\Rector\WrapEnumClassStringRector;
use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\Name\RenameClassRector;

// Order matters where two rules meet one node: positional arguments are named, and the root
// un-nested, while the names are still classic -- before the rename reaches them.
return RectorConfig::configure()
    ->withRules([
        NamePositionalArgumentsRector::class,
        UnnestOpenApiRector::class,
        WrapEnumClassStringRector::class,
        TypeClassStringToRefRector::class,
    ])
    ->withConfiguredRule(RenameClassRector::class, require __DIR__ . '/classic-to-spec-classes.php')
    ->withRules([MoveSchemaKeywordsRector::class])
    ->withConfiguredRule(AliasSpecImportsRector::class, [AliasSpecImportsRector::ALIAS => 'OAS']);
