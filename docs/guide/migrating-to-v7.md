# Migrating to v7

## Overview

`v7` makes hybrid the default mode and deprecates classic. The main changes are:

* Hybrid is the default mode, and spec and hybrid are no longer beta
* The default output version is OpenAPI 3.1.0
* Classic attributes, annotations and the classic pipeline are deprecated, and removed in 8.0
* `nikic/php-parser` 4.x is no longer supported, and the Symfony components need 5.4, or 6.4 or later
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

Without a mode set, the document is built in hybrid mode. Hybrid scans the same classic attributes and
docblock annotations as classic, so for most projects the document stays the same. The known differences:

* **The OpenAPI version.** Hybrid generates OpenAPI 3.1.0 where classic generated 3.0.0. To keep
  3.0.0, set the version: `Builder::setVersion('3.0.0')`, or `--version 3.0.0` on the command line.
* **Unreferenced components.** Components that no path references are removed. That includes
  parameters and responses, and a schema kept only for client code generation. Set `cleanup.enabled`
  to `false` to keep them, see [Behavioral differences](/guide/modes#behavioral-differences).
* **Class docblocks.** A class docblock becomes the description of each schema declared on the
  class that has none of its own. Where a schema listed its properties inline, classic gave the
  docblock to the first of those properties instead, if that one had no description.
* **Properties beside `allOf`.** On a nested property, `properties` declared beside `allOf` stay
  beside it. Classic moved them into an `allOf` entry of their own. Both say the same.
* **Configuration keys.** `-c` takes augmenter keys rather than processor keys, apart from
  `generator.*`. `openapi -D src` lists them.

Hybrid also logs warnings where classic logged nothing. Most of them are classic type names such as
`bool` or `datetime`, which hybrid still translates, with a warning, until 8.0.
`TypeAliasesRector` rewrites them to the OpenAPI types.

Compare the document with one built in classic mode before relying on it. A difference not listed
here is worth an [issue](https://github.com/zircote/swagger-php/issues).

Custom processors run in hybrid mode only [over the annotation instances as scanned](/guide/extension-points#the-classic-escape-hatch).
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

The [Classic Attribute](/reference/attributes), [Docblock Annotation](/reference/annotations) and
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
