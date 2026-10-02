<?php declare(strict_types=1);

/*
 * The `OpenApi\Attributes` -> `OpenApi\Spec` migration.
 *
 *     vendor/bin/rector process src \
 *         --config vendor/zircote/swagger-php/rector/set/classic-to-spec.php --dry-run
 *
 * Run it with `--dry-run` first, always, and generate your document before and after to
 * compare. Run it over everything that uses or extends an attribute, not only the directories
 * your generator scans: a class extending one is migrated here or not at all. It:
 *
 * - names positional arguments to a classic attribute by the classic signature, including
 *   `parent::__construct()` in your own subclasses, since spec orders parameters differently;
 * - lifts `info:`, `servers:`, `tags:` and `externalDocs:` off a root `OpenApi` into sibling
 *   attributes, and renames its `openapi:` to `version:`;
 * - wraps a bare `enum: SomeEnum::class` into a list, and turns a class-string `type:` into
 *   `ref:`, which is what classic read them as;
 * - renames the classes, from a map generated out of both namespaces;
 * - moves schema keywords onto a nested `schema:` where the spec class no longer accepts
 *   them, which is `Property`, `JsonContent` and `XmlContent`;
 * - collapses the references onto `use OpenApi\Spec as OAS;`.
 *
 * It does not touch your generator code: custom processors, config keys, and spec's pruning
 * of unreferenced components, which classic never did, are the migration guide's to cover.
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
 * - `Attributes\PathItem` is a different concept under the same name in spec, so the rename
 *   lands on a class taking different arguments. A root `OpenApi` carrying `paths:`,
 *   `components:` or `webhooks:`, or children not written inline, is left whole for the same
 *   reason.
 * - Spec refuses a merge classic resolved by picking: a stacked `#[Header]` beside more than
 *   one `#[Response]` reports `Ambiguous merge`, because which response owns the header is
 *   not something the code says. On real code this is the common one.
 * - An attribute that already carries its own `schema:` is left as the author wrote it.
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
