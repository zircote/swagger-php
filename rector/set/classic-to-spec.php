<?php declare(strict_types=1);

/*
 * Stage one of the `OpenApi\Attributes` -> `OpenApi\Spec` migration: class and namespace
 * renames only.
 *
 *     vendor/bin/rector process src \
 *         --config vendor/zircote/swagger-php/rector/set/classic-to-spec.php --dry-run
 *
 * **This stage changes no emitted document.** The historic argument names -- `schema:`,
 * `parameter:`, `request:`, `securityScheme:` -- survive on the spec attributes as deprecated
 * aliases until 8.0, so renaming the classes leaves a working build that warns. Run it, review
 * the diff, ship it, and do the argument renames separately; see `classic-to-spec-keys.php`.
 * The window closes at 8.0, when the aliases go.
 *
 * **What this stage does not do**, because neither is a rename:
 *
 * - `Attributes\Property` carries every schema keyword; `Spec\Property` holds a `Schema`. The
 *   keywords move to a sibling `#[OA\Schema]`. Same for `JsonContent` and `XmlContent`, which
 *   become `MediaType\Json` and `MediaType\Xml` holding a schema.
 * - `Attributes\Query` is the HTTP QUERY method operation and becomes
 *   `#[OA\Operation(method: 'query')]`; `Attributes\Webhook` becomes a `webhook:` argument on
 *   an operation attribute.
 * - `Attributes\PathItem` and `Attributes\OpenApi` are different concepts under the same name
 *   in spec, so the rename lands you on a class that takes different arguments.
 *
 * Those are reported by the migration guide rather than transformed. Run with `--dry-run`
 * first, always.
 *
 * This set follows the library's version but carries no backwards-compatibility promise, and
 * goes with classic at 8.0.
 */

use OpenApi\Rector\AliasSpecImportsRector;
use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\Name\RenameClassRector;

return RectorConfig::configure()
    ->withConfiguredRule(RenameClassRector::class, require __DIR__ . '/classic-to-spec-classes.php')
    ->withConfiguredRule(AliasSpecImportsRector::class, [AliasSpecImportsRector::ALIAS => 'OAS']);
