<?php declare(strict_types=1);

/*
 * The `OpenApi\Attributes` -> `OpenApi\Spec` migration.
 *
 *     vendor/bin/rector process src \
 *         --config vendor/zircote/swagger-php/rector/set/classic-to-spec.php --dry-run
 *
 * Run it with `--dry-run` first, always, and generate your document before and after to
 * compare. It does three things:
 *
 * - renames the classes, from a map generated out of both namespaces;
 * - moves schema keywords onto a nested `schema:` where the spec class no longer accepts
 *   them, which is `Property`, `JsonContent` and `XmlContent`;
 * - collapses the references onto `use OpenApi\Spec as OAS;`.
 *
 * The historic argument names -- `schema:`, `parameter:`, `request:`, `securityScheme:` --
 * are left alone. They still work on the spec attributes as deprecated aliases, emit the same
 * document, and warn; renaming them to `component:` is a separate pass you can take at
 * leisure, with the deprecation output as the worklist. That window closes at 8.0.
 *
 * **What it does not do.** These need a person, and the set leaves them for one:
 *
 * - `Attributes\Query` is the HTTP QUERY method operation and becomes
 *   `#[OA\Operation(method: 'query')]`; `Attributes\Webhook` becomes a `webhook:` argument
 *   on an operation attribute.
 * - `Attributes\PathItem` and `Attributes\OpenApi` are different concepts under the same
 *   name in spec, so the rename lands on a class taking different arguments.
 * - Spec refuses a merge classic resolved by picking: a stacked `#[Header]` beside more than
 *   one `#[Response]` reports `Ambiguous merge`, because which response owns the header is
 *   not something the code says. Measured on phpMyFAQ, this is the common one.
 * - An attribute that already carries its own `schema:` is left as the author wrote it.
 *
 * This set follows the library's version, carries no backwards-compatibility promise of its
 * own, and goes with classic at 8.0.
 */

use OpenApi\Rector\AliasSpecImportsRector;
use OpenApi\Rector\MoveSchemaKeywordsRector;
use Rector\Config\RectorConfig;
use Rector\Renaming\Rector\Name\RenameClassRector;

return RectorConfig::configure()
    ->withConfiguredRule(RenameClassRector::class, require __DIR__ . '/classic-to-spec-classes.php')
    ->withRules([MoveSchemaKeywordsRector::class])
    ->withConfiguredRule(AliasSpecImportsRector::class, [AliasSpecImportsRector::ALIAS => 'OAS']);
