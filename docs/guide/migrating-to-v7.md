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
| `DefaultLogger` | Any PSR-3 logger, passed to the `Builder` constructor |
| `Builder::setMode()`, `Mode`, CLI `--mode` | Leave them out. Hybrid is the default, and builds spec attributes as spec mode does. |
| `HybridBridge` | Nothing. It goes with classic. |
| `Result::openApi()` | `Result::specification()` |
| `Serializer` | No replacement yet |
| Raw security arrays on spec attributes, `['bearerAuth' => []]` | `Security\Requirement`. `SecurityToRequirementRector` rewrites them. |
| `ComponentIndex::buildRefMap()` | `ComponentIndex::findByKey()`, which takes the bucket the use means |
| `OpenApi31Compiler::refString()` | `RefInterface::getRefValue()`, on the attribute that holds the `ref` |
| `ResolverInterface`, reported at runtime for each resolver implementing it alone | `KeyResolverInterface`. `KeyResolverTrait` supplies `resolve()`, so only `resolveKey()` needs writing. |
| `Builder::setLogger()` | The `Builder` constructor, `new Builder($logger)` |

The [Classic Attribute](/reference/attributes), [Docblock Annotation](/reference/annotations) and
[Processor](/reference/processors) references mark each deprecated class.

## Changed output in 7.1

A class used to stand for whichever of its components was indexed last, in any bucket, without a
warning. Three cases change:

- **A class with more than one component in a bucket.** A `$ref` given as the class, or a
  property typed with it, now resolves to none of them and logs a warning.
  [Describing a class more than once](/guide/extension-points#describing-a-class-more-than-once)
  has the ways to pick one.
- **A class with a schema and a component in another bucket.** A property typed with the class
  references the schema. It used to reference the other component, a response for example,
  which is not valid in a schema.
- **A request body declared on a parameter whose class has no request body component.** It is
  left empty and logs a warning. It used to reference the class's schema, which is not valid for
  a request body.

## Changed output in 7.2

A component whose key has qualifiers, and no explicit name, is named after its key:
`Author-groups.summary` rather than `Author`. It used to take its class's name and clash with the
plain component, and the document kept only one of them.
[Describing a class more than once](/guide/extension-points#describing-a-class-more-than-once)
has the scheme.

A component schema declared on a method no longer takes its description and `deprecated` from the
method's docblock. A schema declared on a class still takes them from the class docblock.

Parameters, response headers and inline schemas that describe a property take its docblock's
description, `@example` and `@deprecated`, as the property's own schema already did.
[Summary and description](/guide/augmentation#summary-and-description) has the details.

`Spec\Header::$description` defaults to `Undefined::UNDEFINED` rather than `null`, so a description
can be inferred for it. Code checking for the default with `=== null` uses `Undefined::isDefault()`
instead.

A type resolver set on `Types` with `setTypeResolver()` is replaced when the build runs, by the
one every augmenter reading PHP types shares. Set it with `Builder::setTypeResolver()` instead.

## Changed output in 7.3

`Result::isValid()`, `log()`, `warnings()` and `errors()` include what the source scan, the
resolvers and skipped classes log. An error a resolver logs now makes `isValid()` false, as it
already made the CLI exit with an error.

## Changed in 7.3

`Builder::$logger` holds, and `Builder::getLogger()` returns, a `CollectingLogger` that forwards
to the logger the builder is given. A subclass assigning its own logger to `$logger` wraps it
first, `new CollectingLogger($logger)`. A subclass overriding `getLogger()` declares
`CollectingLogger` as its return type.

## Changed output in 7.4

A public property with neither a property nor a schema attribute no longer puts its class in
`components`. With `cleanup.enabled` off, such a class used to show there, unreferenced. Where
nothing else adds the class, an operation parameter, a getter or a docblock type such as
`list<Part>` typed with it used to reference that component. Now that type is left out, with a
warning. Add the class as a source, or document the property.

In classic mode, a docblock `@example` takes the schema's type: `@example 1` on an `int` property
writes `1` rather than `"1"`. A value that does not match the type, or any example on a schema that
allows a string, stays as written. `#[OA\Examples(value: false)]` writes `false` rather than `0`,
and a float stays a number.

## Changed in 7.4

`Resolver::getTypedReflectors()` is replaced by `getPropertyTypes()`, which takes the schema rather
than its class, and the attribute factory that reads its traits. A subclass overriding it
overrides `getPropertyTypes()` instead. `findUnresolved()` and `collectFromReflectors()` take the
attribute factory too.

## Changed output in 7.5

Spec and hybrid log an `error`, rather than a `warning`, where the document is invalid or something
declared is lost. Such a build makes `isValid()` false, and the CLI exits with 1:

* the document is invalid: `paths`, or all of `paths`, `webhooks` and `components`, missing; a
  license with both `url` and `identifier`; a duplicate `operationId`; a path parameter that is
  not required; an invalid response key or schema type; a missing key field; a tag whose parent
  does not exist; in 3.0, an array without `items`; a reference that resolves to nothing, or to
  several components;
* something declared is lost: a class or member skipped; a `\Reflector` source that is not a
  class; a component declared on a member, or declared twice; a media type declared twice;
  attributes on a constructor; an `operationId` on an operation with several methods; a security
  requirement given as a bare scheme name; a request body or type whose class has no schema, or
  several; an operation without a method;
* in 3.0, `prefixItems`, `unevaluatedProperties`, `unevaluatedItems` and `if`/`then`/`else`, which
  are kept and make the document invalid;
* an unknown config option or key.

Classic type aliases and the array form of a security requirement log a `notice`. A merge
collision stays a `warning`, and so does what a version leaves out because it cannot express it,
such as webhooks in 3.0. Plugins in classic mode stay a `warning` too: classic builds what it did
without them. [Writing messages](/dev/messages) has the rules the levels follow.

## Preparing for 8.0

In 8.0 classic is removed, and so is `setMode()`. Spec attributes move from `OpenApi\Spec` to
`OpenApi\Attributes`. For code already written against `OpenApi\Spec`, that move is one `use`
line per file.

The classic component name fields on spec attributes (`schema:`, `parameter:`, `request:`,
`securityScheme:`) are removed with it. [The component names](/guide/migrating-to-spec#the-component-names)
has the details.

`AttributeInterface` gains `isAuthored()`, which `AbstractAttribute` has since 7.4. A class
implementing the interface without extending `AbstractAttribute` adds it.

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
