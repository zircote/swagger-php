# Migrating to v7

## Overview

`v7` makes hybrid the default mode and deprecates classic. The main changes are:

* Hybrid is the default mode, and spec and hybrid are no longer beta
* The default output version is OpenAPI 3.1.0
* Classic attributes, annotations and the classic pipeline are deprecated, and removed in 8.0
* `nikic/php-parser` 4.x is no longer supported, and the Symfony components need at least 6.4
* The elements deprecated for 7.0 have been removed (see below)

For most installations upgrading does not require any code changes.

## Classic, hybrid and spec

`swagger-php` has two sets of attributes, each with its own pipeline:

* **Classic**: `OpenApi\Attributes` and docblock annotations, the original way to document an API.
  The `Generator` scans them and runs its processors over the result.
* **Spec**: `OpenApi\Spec` attributes, added in 6.5.0. They are typed objects that can also be
  built by hand. An augmenter pipeline fills in what can be inferred, and a compiler writes the
  chosen OpenAPI version.

**Hybrid** sits between the two. It scans classic attributes and annotations and hands them to the
spec pipeline. It also builds spec attributes, so both can be mixed in one codebase while migrating.

|              | v6      | v7         | v8                                |
|--------------|---------|------------|-----------------------------------|
| Default mode | classic | hybrid     | spec                              |
| Classic      | stable  | deprecated | removed                           |
| Hybrid       | beta    | deprecated | removed                           |
| Spec         | beta    | stable     | stable, moved to `OpenApi\Attributes` |

[Processing Modes](/guide/modes) compares the modes in detail, and
[Why spec attributes?](/guide/why-spec) explains the reasons for the change.

## The default mode

Without a mode set, the document is built in hybrid mode. Hybrid scans the same attributes and
annotations as classic, so the document stays the same, with these exceptions:

* **The OpenAPI version.** Hybrid generates OpenAPI 3.1.0 where classic generated 3.0.0. To keep
  3.0.0, set the version: `Builder::setVersion('3.0.0')`, or `--version 3.0.0` on the command line.
* **Unreferenced components.** Components that no path references are removed. Set
  `cleanup.enabled` to `false` to keep them, see [Behavioral differences](/guide/modes#behavioral-differences).
* **Configuration keys.** `-c` takes augmenter keys rather than processor keys, apart from
  `generator.*`. `openapi -D src` lists them.

Custom processors run in hybrid mode only [over the annotations as scanned](/guide/extension-points#the-classic-escape-hatch).
A project that depends on them can stay on classic with `Builder::setMode(Mode::CLASSIC)`, or
`--mode classic`, while the processors are rewritten as augmenters.

## Deprecations

Everything below still works in v7 and is removed in 8.0.

| Deprecated | Replacement |
|---|---|
| `OpenApi\Attributes` and docblock annotations | `OpenApi\Spec` attributes. The [Rector set](/guide/migrating-to-spec) rewrites attributes. |
| Processors, `Builder::withGenerator()` | An [augmenter](/guide/extension-points#augmenters), registered with `Builder::withAugmenters()` |
| CLI `--add-processor` (`-a`) and `--remove-processor` (`-r`) | `Builder::withAugmenters()` |
| `Generator`, `Analysis`, `Context` and the analysers | `Builder` |
| `ComposerAutoloaderScanner` | `Builder::addSource()`, with paths or `\ReflectionClass` instances |
| `TypeResolverInterface`, `TypeInfoTypeResolver` | The augmenter pipeline infers types. A custom rule is an augmenter. |
| `DefaultLogger` | Any PSR-3 logger, set with `Builder::setLogger()` |
| `Builder::setMode()`, `Mode`, CLI `--mode` | Leave them out. Hybrid is the default, and builds spec attributes as spec mode does. |
| `HybridBridge` | Nothing. It goes with classic. |
| `Result::openApi()` | `Result::specification()` |
| `Serializer` | No replacement yet |
| Raw security arrays on spec attributes, `['bearerAuth' => []]` | `Security\Requirement`. `SecurityToRequirementRector` rewrites them. |

The [Attribute](/reference/attributes), [Annotation](/reference/annotations) and
[Processor](/reference/processors) references mark each deprecated class.

## Preparing for 8.0

In 8.0 classic is removed, and so is `setMode()`. Spec attributes move from `OpenApi\Spec` to
`OpenApi\Attributes`. For code already written against `OpenApi\Spec`, that move is one `use`
line per file.

The classic component keys on spec attributes (`schema:`, `parameter:`, `request:`,
`securityScheme:`) are removed with it. [The component keys](/guide/migrating-to-spec#the-component-keys)
has the details.

## Removed deprecated elements

### `LegacyTypeResolver`
Types are resolved with `TypeInfoTypeResolver`, which was already the default.

### Classes moved in 6.x
The aliases left at the old names are gone:

* `OpenApi\Analysers\TokenScanner` is `OpenApi\Utils\TokenScanner`
* `OpenApi\Pipeline` is `OpenApi\Utils\Pipeline`
* `OpenApi\SourceFinder` is `OpenApi\Utils\SourceFinder`
* `OpenApi\Utils\TypeMapper` is `OpenApi\Type\TypeMapper`

### `TypeResolverInterface::NATIVE_TYPE_MAP`
Use `OpenApi\Type\TypeMapper::NATIVE_TYPE_MAP`.
