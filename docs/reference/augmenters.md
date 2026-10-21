# Augmenter Reference

This page is generated automatically from the `swagger-php` sources.

For improvements head over to [GitHub](https://github.com/zircote/swagger-php) and create a PR ;)


Augmenters enrich the collected specification with inferred data before compilation.
They run in three groups — **resolve** (type inference, refs), **reduce** (filtering, cleanup),
and **augment** (docblocks, operation ids, tags) — and are listed below in execution order.

Augmenters run in hybrid and spec mode.

## Augmenter Configuration

### Command line
The `-c` option takes a name/value pair: the augmenter name (starting lowercase)
and the option name, separated by a dot (`.`).

To list the available augmenter names and options use `-D`. It still requires a
source path, e.g. `./vendor/bin/openapi -D src`. Unknown keys are
reported as warnings.

```shell
> ./vendor/bin/openapi -c operationIds.hash=true src
> ./vendor/bin/openapi -c pathFilter.tags[]=/pets/ -c pathFilter.tags[]=/store/ src
```

### Programmatically with PHP
Configuration can be set using the `Builder::withAugmenters()` method to access the pipeline
and configure individual augmenters via `Pipeline::get()`.

```php
(new Builder())
    ->withAugmenters(function ($pipeline) {
        $pipeline->get(Augmenter\OperationIds::class)->setHash(true);
        $pipeline->get(Augmenter\PathFilter::class)->setTags(['/pets/', '/store/']);
    });
```

## Default Augmenters

### [TypeAliases](https://github.com/zircote/swagger-php/tree/master/src/Augmenter/TypeAliases.php)

::: warning Deprecated
Since 6.15, removed in 8.0 - write the OpenAPI type; `TypeAliasesRector` rewrites it
:::

Translates the type names classic accepts in an explicit `type` into OpenAPI types.

Classic maps `int`, `bool`, `long`, `float`, `double`, `date` and the date-time class names
through {@see TypeMapper::NATIVE_TYPE_MAP}, so `type: 'long'` becomes `integer` with format
`int64`. The format is set only where none is given, and in a type list each name gets its
type alone, as in classic. Each translation logs a deprecation. `mixed` and `byte` are left
as they are, and the compiler reports them as unknown types.

Runs first, so every augmenter after it sees valid types, and a schema that `Inheritance`
copies is translated, and reported, once.

#### Config settings
- **typeAliases.enabled** : `bool` · default: `true`  
  No details available.

### [Inheritance](https://github.com/zircote/swagger-php/tree/master/src/Augmenter/Inheritance.php)

Handle all scenarios related to PHP inheritance.

Delegates to:
- Inheritance\Schemas
- Inheritance\Operations

### [OperationTags](https://github.com/zircote/swagger-php/tree/master/src/Augmenter/OperationTags.php)

Adds each tag declared on a method or a class to the operations declared there.

A tag on a method goes to that method's operations, and a tag on a class to every operation
whose class it is, the ones on its methods included. An operation's own `tags` come first,
then its method's, then its class's, each name once. A tag with no operations to go to, such
as one on a class holding only `Info`, is left a declaration.

An operation belongs to the class its reflector names. For one inherited into a child
controller that is the child, so it takes the child's class tags.

### [ImpliedSchemas](https://github.com/zircote/swagger-php/tree/master/src/Augmenter/ImpliedSchemas.php)

Adds the schemas the document's types imply but nobody declared.

A property, parameter or request body typed with a class that has no schema would lose its
type, since only a component can be referenced. Where the class can be described without
guessing, it is added as if it carried `#[OA\Schema]`, and the augmenters after this one name,
fill and reference it as they would a declared schema.

Today that is a PHP enum, whose cases are the whole description: `Names` names it, `Enums` fills
its cases and `Types` references it. A backed enum's schema takes the backing type, so it lists
the backing values, which is what a backed enum serialises to. A bare `#[OA\Schema]` on the enum
lists the case names instead.

### [Names](https://github.com/zircote/swagger-php/tree/master/src/Augmenter/Names.php)

Infers component keys from PHP reflectors when not explicitly set.

A component declared on a class is named after that class, so it can be referenced by
class name. Declared anywhere else — a method, a parameter — the class reflector belongs
to the *declaring* class, whose name is already taken by that class's own component, so
nothing is inferred and the component stays inline.

A class carrying `#[OA\Components]` is the exception: it is a holder, so each of its members
declares a component of its own, named after the member.

A parameter is keyed differently: its `name` is its identity in OpenAPI, so a parameter
component is keyed by it wherever it was declared, holder or not, and falls back to the class name only
when it has no name either.

### [Enums](https://github.com/zircote/swagger-php/tree/master/src/Augmenter/Enums.php)

Expands PHP enums into schema enum values.

For schemas attached to a PHP enum, determines schema name, type, and enum values.
Also resolves UnitEnum instances and enum class-strings in any schema's or server
variable's enum array.

Rules for name vs. value:
- Unit enums (not backed): always use case names, type becomes "string"
- Backed enums without explicit schema type: use case names, type becomes "string"
- Backed enums with schema type matching backing type (int→"integer", string→"string"):
  use backing values, type preserved
- Backed enums with schema type NOT matching backing type: use case names

#### Config settings
- **enums.enumNames** : `string` · default: `null`  
  If set, stores enum case names in a vendor extension with this key (e.g. <code>x-enum-varnames</code>).

### [Shortcuts](https://github.com/zircote/swagger-php/tree/master/src/Augmenter/Shortcuts.php)

Resolves shortcut attributes into the plain attributes they stand for.

A shortcut carries schema keywords on an attribute that holds a schema, so the common case
needs no nested `OA\Schema`. This augmenter moves those keywords to where the document has
them.

Handles:
* `OA\MediaType\Json` and `OA\MediaType\Xml`: their schema keywords move into the media
  type's `schema`;
* `OA\Schema\Items`: an array schema, whose item keywords move into a new `items` schema.

### [DefaultMediaType](https://github.com/zircote/swagger-php/tree/master/src/Augmenter/DefaultMediaType.php)

Sets the media type of every `MediaType` that has none.

That is a bare `Schema` given as `content`, or a `MediaType` written without `mediaType`.
A media type that is set is left alone, so an explicit one is how a single response or
request body departs from the default.

#### Config settings
- **defaultMediaType.mediaType** : `string` · default: `application/json`  
  The media type given to content that names none.

### [SecurityRequirements](https://github.com/zircote/swagger-php/tree/master/src/Augmenter/SecurityRequirements.php)

Drops security requirements written as a bare scheme name, and reports raw arrays.

Classic reads `security: ['bearerAuth']` as `bearerAuth` with no scopes. Spec takes a
`Security\Requirement` per entry, so a string is reported and dropped. An operation left with
no requirements falls back to the ones above it, rather than declaring itself public with an
empty list.

A raw array, `['bearerAuth' => []]` or `['bearerAuth']`, still compiles and is kept, but is
reported: the array form is removed in 8.0, and {@see SecurityToRequirementRector} rewrites it.

Runs before `PathItems`, which clones path item requirements onto their operations.

### [PathItems](https://github.com/zircote/swagger-php/tree/master/src/Augmenter/PathItems.php)

Resolves PathItem prefixes, copies metadata to operations, and sets path-level output.

Composes path prefixes from the PathItems governing each operation's class, prepends them
to operation paths, copies tags/security/responses to operations that don't declare their
own, and marks PathItems that have spec-level output (parameters, summary, description,
servers) with their resolved path. A PathItem governing several paths is written once per
path.

Security requirements, responses and the per-path PathItems are deep copies, so a change
made through one operation or path stays there.

The ancestor walk and the prefix composition belong to `Specification\PathItemHierarchy`.

### [Types](https://github.com/zircote/swagger-php/tree/master/src/Augmenter/Types.php)

Infers schema type, format, nullable, items, etc. from PHP type declarations and docblocks.

Walks all properties and parameters in the specification and fills their schema
fields from the attached reflector's type information.

### [Properties](https://github.com/zircote/swagger-php/tree/master/src/Augmenter/Properties.php)

Infers an object schema's `required` list and its properties' `default` from the PHP declarations.

A property is required when its PHP type is not nullable and it has no default, the rule a
parameter follows, with the default counted too: a value exists when the field is left out. A
promoted constructor property counts by its parameter's default. A property with no PHP type
adds nothing to `required`.

A default is taken where it is a value the document can hold: a scalar, an enum case or an array
of them. An enum case gives the value its schema lists: a backed case's value, a unit case's
name. `null` is not taken, since the nullable type already says it, and neither is a value the
schema's type does not admit.

What the attribute sets wins: an explicit `required` list is kept as written, and so is an
explicit `default`, `null` included. Schemas converted from classic annotations are left alone.

Runs after `Augmenter\Types`, which names each property and gives it the type its default is
checked against.

### [Refs](https://github.com/zircote/swagger-php/tree/master/src/Augmenter/Refs.php)

Resolves FQCN-based $ref values to JSON Reference paths.

Builds a map of class names to their component paths and rewrites
any $ref that looks like a FQCN into the proper #/components/... path.
A ref that is neither a known class nor a pointer to an existing component is reported.

### [Parameters](https://github.com/zircote/swagger-php/tree/master/src/Augmenter/Parameters.php)

Gives a parameter that is only a `ref` the `name` and `in` of the component it points to.

OpenAPI tells two parameters apart by name and location. A `ref` parameter has neither of its
own, so without them it cannot be matched with an inline parameter describing the same thing,
and `Merge\Operations` keeps both. Filled here, after `Augmenter\Types` has named a parameter
from its PHP parameter and `Augmenter\Refs` has rewritten class name refs, every parameter
reaching the merge carries its identity. It has to run before `Augmenter\Merge`, which the
phases see to: this is `Resolve`, and the merge is `Reduce` and later.

Only unset fields are filled, and the output does not change: a parameter with a `ref` is
compiled to the reference alone.

### [Merge](https://github.com/zircote/swagger-php/tree/master/src/Augmenter/Merge.php)

Reduces every root collection to one entry per key, through the registered mergers.

Two attributes can claim one key, such as two operations on the same path and method, or two
schemas named `Pet`. The merger that claims the type says what identifies it and what the
survivor is, and one entry per key reaches the compiler.

Only the root collections, because that is where the two come from different places (a scan,
a `withSpecification()` hook, the resolver, an inheritance clone), and something has to
decide between them. A collision *inside* one attribute is two entries the same author wrote
in one place. The compiler reports it and keeps its own last-write-wins.

Registered twice, and both are this class. The first run is the first pipe of the **reduce**
phase, which is the earliest point every identity exists: `Augmenter\PathItems` resolves an
operation's path and `Augmenter\Names` infers component keys, both in **resolve**. It is also
before `Cleanup` and everything downstream that should see the survivor rather than both
attributes. The second is the last pipe of all, so what a late augmenter adds is reduced too.
It is a grouping over lists and costs nothing when there is nothing to do. An augmenter
registered after it runs after it, and `insert()` is how to land ahead.

### [PathFilter](https://github.com/zircote/swagger-php/tree/master/src/Augmenter/PathFilter.php)

Filters operations by tag and/or path patterns.

If no tags or paths filters are set, no filtering is performed.
All filter expressions must be valid regular expressions (with delimiters).

#### Config settings
- **pathFilter.tags** : `array` · default: `[]`  
  A list of regular expressions to match <code>tags</code> to include.
- **pathFilter.paths** : `array` · default: `[]`  
  A list of regular expressions to match <code>paths</code> to include.

### [Cleanup](https://github.com/zircote/swagger-php/tree/master/src/Augmenter/Cleanup.php)

Removes unreferenced components from the specification.

Iterates multiple times to catch nested dependencies (a schema only
referenced by another unused schema should also be removed).

Removal is reported once per run, as a notice with the count and the switch that turns it
off; the components themselves are listed at debug level. A response component keyed by a
status code is also reported on its own, as a warning, because it is a response that was
meant to nest into an operation.

#### Config settings
- **cleanup.enabled** : `bool` · default: `true`  
  Enables/disables removal of unreferenced components.

### [MediaTypes](https://github.com/zircote/swagger-php/tree/master/src/Augmenter/MediaTypes.php)

Promotes property encodings and re-keys MediaType encoding lists by property name.

Promotes `OA\Encoding` definitions from `OA\Property\Encoded` properties to their
parent MediaType, then re-keys the encoding list as an associative array keyed by
property name (the format the compiler expects).

### [Docblocks](https://github.com/zircote/swagger-php/tree/master/src/Augmenter/Docblocks.php)

Fills summary, description, and deprecated from PHP docblock comments.

Walks all attributes in the specification that have summary/description
properties and populates them from the reflector's docblock when not
explicitly set.

### [OperationIdTemplates](https://github.com/zircote/swagger-php/tree/master/src/Augmenter/OperationIdTemplates.php)

Expands the placeholders in an explicit operationId.

`{method}` is replaced by the operation's method, as stored, in lowercase. Any other `{...}` is
left as written.

### [OperationIds](https://github.com/zircote/swagger-php/tree/master/src/Augmenter/OperationIds.php)

Generates operationId for operations that don't have one explicitly set.

The id is derived from method, path and the declaring method, function or class. An
operation with no reflector is identified by method and path alone.

#### Config settings
- **operationIds.hash** : `bool` · default: `true`  
  If set to <code>true</code> generate ids (md5) instead of clear text operation ids.

### [Tags](https://github.com/zircote/swagger-php/tree/master/src/Augmenter/Tags.php)

Ensures all tags used on operations exist in the global tags list.

Adds missing Tag objects for any tag name referenced by operations.
Removes unused declared tags unless whitelisted.

#### Config settings
- **tags.whitelist** : `array` · default: `[]`  
  Whitelist tags to keep even if not used. Use '*' to keep all.
- **tags.withDescription** : `bool` · default: `true`  
  Enables/disables generation of default tag descriptions.

### [EnumDescriptions](https://github.com/zircote/swagger-php/tree/master/src/Augmenter/EnumDescriptions.php)

Generates a description for enum-based properties.

#### Config settings
- **enumDescriptions.enabled** : `bool` · default: `false`  
  Enables/disables generation of descriptions for enum based properties.
