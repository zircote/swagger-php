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
| `AttributeFactory::getTokenScanner()` | `AttributeFactory::getSourceInspector()` |
| `TokenScanner` | `SourceInspector`, the `AttributeFactory` default |
| `x` on `Security\Requirement` | Nothing. A Security Requirement Object cannot carry extensions, so it is left out with a warning. |

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
without them.

The messages themselves are reworded in spec and hybrid, so they start with what they are about
and end with where it was declared. `Ref: dangling reference '#/components/schemas/Pet' — no such
component` now reads `Reference "#/components/schemas/Pet" points to no component`, and
`Skipping unknown Foo` reads `Class "Foo" not found, skipped`. A test matching log lines in full
needs updating. Classic's messages are unchanged. [Writing messages](/dev/messages) has the form
and the rules the levels follow.

## Changed output in 7.6

In classic and hybrid mode, a trait imported through a grouped `use`, such as
`use App\Model\{HasTimestamps, SoftDeletes};`, is resolved. A class using such a trait now gets
the trait's properties. Before, they were missing, with an `Unexpected @OA\Property()` warning.

Spec and hybrid run the validation checks in one pass over the document, so messages from
different checks come out mixed, rather than one check after the other. Each check's own messages
keep their order. A test matching the full list of messages in order needs updating.

## Changed in 7.6

`AttributeFactory` takes any `SourceInspectorInterface` where it took a `TokenScanner`, and asks it
which members and traits are a class's own. The default is `SourceInspector`, which answers from
reflection and finds a file's classes from its tokens, so a spec build parses no PHP. `TokenScanner`
implements the interface too, and classic analysis keeps using it. Passing `new TokenScanner()` to
the `AttributeFactory` gets the parser back until 8.0, though `TokenScanner` itself is deprecated.

A subclass reading `$tokenScanner` gets the interface: it calls `declares()`, `directTraits()` and
`classesIn()` rather than `detailsFor()` and `scanFile()`. With the default `SourceInspector`, a call
to `detailsFor()` or `scanFile()` there fails with an undefined method error.

`SourceInspector` and `TokenScanner` answer `declares()` differently in two cases:

* **A class declared in `eval()`'d code** has no file to parse. `TokenScanner` treats all its
  members as its own and finds no traits. `SourceInspector` tells the trait members apart, except
  a property or constant the class repeats word for word from a trait, which it counts as the
  trait's.
* **A property promoted by a trait's constructor** is not declared by the trait for
  `TokenScanner`, which only reads promoted properties in classes. `SourceInspector` says it is.
  The generated spec is the same either way, since `AttributeFactory` reads promoted properties
  through the constructor's parameters and does not ask about them.

`Parameter`, `RequestBody`, `Response`, `Header`, `Link` and `Security\Scheme` take a `summary`,
written beside a `$ref` from OpenAPI 3.1 on. A subclass declaring its own `$summary` property needs
it as `public ?string $summary`. Any other type or visibility is a fatal error.

## Changed output in 7.7

Spec and hybrid report every property the target OpenAPI version leaves out. Before, most were left
out without a message:

* 3.0 and 3.1 warn about a tag's `summary`, `parent` and `kind`, for example
  `Tag "pets" uses summary, not supported in OpenAPI 3.1, omitted`.
* 3.0 also warns about an info `summary`, and about the schema keywords it has no place for:
  `contains`, `minContains`, `maxContains`, `patternProperties`, `propertyNames`,
  `dependentRequired`, `dependentSchemas` and `contentSchema`.

Two existing warnings read like the others now. A license `identifier` at 3.0 gives
`License uses identifier, not supported in OpenAPI 3.0, omitted; use url instead`, and media type
components at 3.0 or 3.1 give `Components uses mediaTypes, not supported in OpenAPI 3.1, omitted`.
A test matching either message needs updating.

Extensions set with `x` on `#[OA\Components]` are written on the document's `components`, and those
on a property on that property's schema. Before, both were left out without a warning. `x` on a
`Security\Requirement` is still left out, now with a warning.

## Changed in 7.7

A compiler subclass leaves a property out by listing it in `UNSUPPORTED_PROPERTIES`. `filter()` then
drops it, and the compiler warns about it. The 3.0 compiler's own `compileInfo()` and
`compileLicense()`, and the 3.2 compiler's own `compileTag()`, are gone, since the lists do what they
did. A subclass calling one of them through `parent::` gets the same result as before.

## Changed output in 7.8

In spec, a 3.2 document writes an inline response's `summary`. Before, it was left out with a
warning.

In spec, a 3.2 document writes an operation with a method such as `COPY` to `additionalOperations`.
Before, it was left out with a warning.

In spec and hybrid, 3.0 and 3.1 leave out `style: cookie` with a warning. Before, it was written,
and the document was invalid.

## Changed output in 7.9

A union of integer ranges, such as `int<1, 5>|int<10, 20>`, becomes an `anyOf` with one integer
schema per range. Before, any union of two ranges was described as `non-zero-int`, which accepts
every integer but 0. `negative-int|positive-int` is still `non-zero-int`.

A docblock string pseudo-type that rules out the empty string, such as `non-empty-string` or
`non-falsy-string`, sets `minLength: 1`. Before, it was a plain `string`.

In spec and hybrid, `items` or `additionalProperties` given on an attribute without a type take the
type from the docblock, as classic does. Before, `@var list<positive-int>` with
`items: new OA\Schema()` came out as `items: {}`.

In spec and hybrid, a 3.0 warning about a schema nested in a property's or parameter's schema, such
as its `items`, ends with the location of that property or parameter. Before, it had no location.

## Changed in 7.9

`Property` takes a `required` flag. A subclass declaring its own `$required` property needs it as
`public ?bool $required`. Any other type or visibility is a fatal error.

`Undefined`, `ComponentKey`, `JsonPointer`, `SourceLocation`, `SecurityRequirement`,
`ServerVariableEnum` and `Schema\AdditionalProperties` are no longer `final`, and the `private`
members in `src` are `protected`. A protected method is not a fixed API: its signature can change
without a deprecation.

## Changed output in 7.10

In hybrid, `callbacks:` on a classic `Components` is written to `components.callbacks`. Before, it
was left out without a message, and a `$ref` to one of its callbacks pointed at nothing.

A document passed to `Builder::withDocument()` keeps its `components.callbacks`. Before, they were
reported as unsupported and left out.

In spec and hybrid, a 3.2 document leaves out a tag's `parent` that names no tag of the document
whenever it is compiled. Before, it was left out only when `validate()` ran first.

In spec and hybrid, an encoding without a name is an error,
`Encoding is missing the key field encoding, omitted`, wherever its media type sits. Before, one in
the content of an operation's request body, response or parameter, or of a request body, response
or parameter component, was left out without a message. Errors make `Result::isValid()` return
`false`, and the CLI exits with an error.

In spec and hybrid, a 3.0 document reports `prefixItems`, `unevaluatedItems`,
`unevaluatedProperties`, `if`, `then` and `else` as warnings, one per keyword:
`uses prefixItems, not supported in OpenAPI 3.0, omitted`. Before, each was an error, and `if`,
`then` and `else` were reported together. The document does not change, since none of them was
written before either.

In spec and hybrid, a message about a schema, encoding, header, link or example nested in another
attribute ends with the location of the nearest attribute holding it. Before, it ended with
`in unknown`.

## Changed in 7.10

A spec `Components` list holding anything other than spec attributes throws an `OpenApiException`
naming the list. A classic `callbacks:` migrated as it is does this. See
[What it leaves for you](/guide/migrating-to-spec#what-it-leaves-for-you).

`OpenApi32Compiler::validate()` no longer sets a tag's unknown `parent` to `null`. The compiler
leaves it out of the document instead, and the `Tag` keeps it.

`Augmenter\MediaTypes` no longer keys a media type's `encoding` list by name. The compiler does.

## Changed output in 7.11

In spec and hybrid, the message for a union member typed with a class that matches no schema, or
several, such as `Money` in `int|Money`, ends in `omitted with the rest of its union`. Before, it
ended in `omitted`, though the whole union was left out, `int` with it.

In spec and hybrid, a class declaring several schemas, exactly one of them without a name, is
referenced as that one: a class-name `ref` and a property typed with the class point at it. Before,
the reference was reported as ambiguous and pointed at nothing.

In spec and hybrid, these messages end with the location of the nearest attribute holding a nested
one, as the other nested messages do since 7.10: an unknown schema type, a property without a name,
an `Example` object in a schema's `examples`, a path parameter that is not required, and in 3.2 an
example with conflicting values. Before, they ended with `in unknown`. A schema message that names
the property or parameter holding the schema, such as an array without `items`, also goes on to the
nearest attribute with a location where that member has none.

In spec and hybrid, the notice for a classic type alias such as `type: 'float'` names the nearest
attribute with a location when the schema is nested more than one level deep, or is held by an
attribute without one. Before, it ended with `in unknown`.

A keyword beside `$ref` that has the same value in the referenced component, such as `type:
'object'` next to a reference to an object schema, is dropped without a warning, since nothing is
lost. One that differs still warns.

A `$ref` to another document, such as a URL or a file (`common.yaml#/components/schemas/Pet`), is
no longer looked up as a class name and reported as matching no component. It is written as given,
as before.

A reference that matches no component, names several, or points to no component is reported once
for each place it is written, with that place's location. The location goes before any hint, as in
`Reference "App\Models\Station" matches no component in App\Http\StationController::index() in …`.
Before, each reference was reported once, without a location.

A docblock type naming a class that is a collection, one implementing `ArrayAccess` or
`Traversable` such as a Laravel resource, references the class's schema where it has one. Before, it
was written as an array of anything, and a list of such a class as an array of arrays, in classic
too. A collection class without a schema stays an array of anything, and one with type parameters,
such as `Collection<int, User>`, is mapped as before. A request body parameter typed with such a
class in its docblock, a Laravel `FormRequest` among them, now names the class as one with the
native type does, and is reported where the class is no request body.

## Changed in 7.11

`Augmenter\ComponentKeys` is a new default augmenter, between `ImpliedSchemas` and `Names`. A
`Builder` subclass returning its own list from `getDefaultAugmenters()` adds it there. Without it,
a class declaring several schemas is reported as ambiguous, as before.

`RefInterface::getRefKey()` returns `null` for a ref to another document, such as a URL or a file,
as it does for a JSON pointer. A class implementing the interface without `RefTrait` does the same,
or its ref is looked up as a class name and reported as matching no component.

`OpenApi31Compiler::validateSchemaType()` takes the schemas' owners as a second argument, and
`collectLocatedHolders()` is replaced by `Specification\LocatedHolders`, which
`Augmenter\TypeAliases::$holders` now holds too. A subclass overriding `validateSchemaType()`
adds the argument.

## Preparing for 8.0

In 8.0 classic is removed, and so is `setMode()`. Spec attributes move from `OpenApi\Spec` to
`OpenApi\Attributes`. For code already written against `OpenApi\Spec`, that move is one `use`
line per file.

The classic component name fields on spec attributes (`schema:`, `parameter:`, `request:`,
`securityScheme:`) are removed with it. [The component names](/guide/migrating-to-spec#the-component-names)
has the details.

`AttributeInterface` gains `isAuthored()`, which `AbstractAttribute` has since 7.4. A class
implementing the interface without extending `AbstractAttribute` adds it.

`TokenScanner` goes with classic, and so does the `nikic/php-parser` dependency, since nothing else
in swagger-php parses PHP. A project using php-parser directly requires it itself.

A spec attribute's constructor ends with `x`, `attachables` and `fan`. A parameter added in a 7.x
minor goes after them, so that arguments passed by position keep working, `summary` on
`Parameter` since 7.6 for example. In 8.0 these parameters move to their place before `x`, and
`x`, `attachables` and `fan` are the last three again. Code that passes them by name is not
affected. Code that passes anything from `x` on by position breaks.

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
