# Spec pipeline internals

The parts of the spec pipeline that are easiest to get wrong when reading the source. For
how the pipeline fits together, see [Architecture](/reference/architecture).

## The DTOs are mutable

Spec attributes are plain data containers, but they are **not** immutable, and neither is
`Specification`. Augmenters assign to attribute properties throughout. `Refs::mergeAllOf()`
nulls `$schema->properties`, `Types` fills schema fields in place, and `Specification` exposes
public arrays that `add()` appends to.

What *is* true is that they carry no serialization logic. Serialization is the compiler's job.

## Slot maps: the slot belongs to the parent

Nesting is declared by the child, via two methods on `AttributeInterface`:

- `merge()`: how this attribute composes into a **sibling** on the same reflector
- `contained()`: which **outer-level** attribute types can absorb it from an inner level

Both return `[TargetClass => 'slot']`, and in both cases the slot names a property on the
**target**, not on the attribute declaring it:

```php
// OA\Property
public function contained(): array
{
    return [Schema::class => 'properties[]'];   // `properties` lives on Schema
}
```

A `[]` suffix appends to a collection. A bare name assigns a scalar. It is easy to read
backwards.

The target's own type has to admit the class declaring the slot. `Schema` once named
`Schema::$properties`, a `list<Property>`, which crashed the inheritance augmenter as soon as
anything reached it. `SlotMapConsistencyTest` checks every slot against its target.

Because the child declares the relationship, downstream code can extend the system: a
custom attachable names its own nesting targets without touching any native attribute.

### How resolution runs

Resolution is driven purely by these declarations. It reads nothing from PHP's own structural
semantics:

1. **Sibling merge**: attributes on the same reflector compose via `merge()`
2. **Hierarchical absorb**: what remains flows upward a level at a time via `contained()`,
   first match wins

Sibling merge chains resolve inner-to-outer, not in declaration order. A `MediaType` stacked
with a `Response` and an `Operation` finds its `Response` before the `Response` is folded into
the `Operation`, whichever way the three are declared. Only attributes whose types name each
other as merge targets cannot be ordered that way, and resolve in declaration order instead.
That is possible with custom attachables, not among the native attributes.

If a level has containers, an unmatched non-root attribute is an error. If a level has no
containers at all, unmatched attributes pass through to the level above.

### What survives resolution

Only *root* attributes should remain, and those are what enters the Specification. A root
attribute is one that can stand alone. It owns a bucket and needs no parent.

- **Always root**: `Schema`, `Operation`, `PathItem`, `OpenApi`, `Info`, `Tag`, `Server`,
  `ExternalDocumentation`, `Security\Scheme`, `Components`, `Attachable`
- **Conditionally root**, when `component` is set (the name the attribute is filed under in
  `components`) and `ref` is not: `Response`, `Parameter`, `Link`, `RequestBody`, `Header`,
  `Example`, `MediaType`. The request body has no `ref` check, since its name can never double
  as a nesting key. Until 8.0 the historic spellings still count for the response and the link
  (`response`, `link`). The other historic spellings alias onto `component` in the constructor
  and need no clause
- **Never root**: `Property`, which must nest inside a parent

Each attribute decides for itself, in `isRoot()`. The one field behind the conditional rule,
`component`, is read through `Specification\ComponentName::of()` everywhere a name is needed:
the compiler, `ComponentIndex` and `Augmenter\Cleanup`. It is filled in from the historic
spellings once per build by `ComponentName::normalise()`, after the contribution hooks and
before the resolver builds the first index.

Root does not mean un-nested. `isRoot()` says what an attribute may be when nothing consumes
it. `merge()` and `contained()` run first, and often do. `Schema` is always root, and is
routinely merged into a sibling, such as a `Property` or a `MediaType`, instead of reaching the
Specification at all.

The user-facing version of this distinction is in [Using Spec
Attributes](/guide/spec-attributes#components). This list is the full one.

## Directory layout is not the class hierarchy

`src/Spec/` nests directories for readability, not inheritance. Notably:

- `Property extends AbstractAttribute`, **not** `Schema`. Being siblings is what lets
  `#[OA\Property]` and `#[OA\Schema]` stack on the same target.
- `Encoding extends AbstractAttribute`, **not** `MediaType`, despite `Property\Encoded`.
- `Contact`, `License`, `ServerVariable` extend `AbstractAttribute`, not `Info`/`Server`.
- `OA\Security` is a namespace, not a class. The classes are `Security\Requirement` and
  `Security\Scheme`.

Genuinely nested: `Schema\{AdditionalProperties,Items,Ref}`, `Property\Encoded`,
`Operation\*`, `Parameter\*`, `MediaType\{Json,Xml}`, `Flow\*`, `Security\Scheme\*`.

The [Spec Attributes reference](/reference/spec-attributes) lists every attribute with its
parameters and what it can nest into.

### What belongs in `Utils/`

A class goes in `Utils/` when it is about PHP rather than about OpenAPI, and the rest of the
tree would use it whatever the pipeline compiled: `TypedList`, `JsonPointer`, `TokenScanner`,
`SourceFinder`. Everything else belongs to a subsystem, and the directory named for that
subsystem already exists. A logger goes in `Loggers/`, a `Specification` traversal in
`Specification/`, a type concern in `Type/`.

Two things this rule is not. It is not about who calls a class. `AttributeFactory` stays
because the Assembler and the augmenters both manufacture spec objects from reflection, and a
class two subsystems share is not owned by either. And it is not about visibility, which is
what makes `Contracts/` narrower than "the public interfaces". What it holds describes
OpenAPI concepts, so replacing the implementation behind one leaves the contract standing.
`PipeInterface` is a pipe in a generic pipeline. Swap `Utils\Pipeline` for a library and the
interface leaves with it, which is why it sits beside `Pipeline` rather than in `Contracts/`.

Moving one is a rename plus imports, and a deprecated subclass left at the old location for
anything documented, until the next major removes it.

## Reflectors are the glue

Every root DTO keeps the reflector it came from. This is how relationships that span
buckets get resolved after assembly, without the DTOs having to reference each other:

- **PathItem to Operation**: a PathItem sits on a class, operations on its methods. The
  `PathItems` augmenter uses `ReflectionMethod::getDeclaringClass()` to pair them
- **Prefix composition**: `ReflectionClass::getParentClass()` walks ancestors so parent
  PathItems can contribute path prefixes. The walk is `Specification\PathItemHierarchy`, not
  repeated per caller
- **OperationId generation**: class and method names come from the reflector
- **Type inference**: `Types` reads PHP type declarations off property and parameter reflectors.
  `Properties` reads nullability and defaults off the same reflectors
- **Naming**: a component schema takes its name from the class it sits on. A property takes its
  name from the property, parameter or constant it sits on

This is what keeps the Assembler concerned only with nesting.

A method reflector supplies no name. `getUnnamed()` is not the property `unnamed`, and the
pipeline does not guess at accessor prefixes. So a bare `Schema` becomes a `Property` only
where the reflector declares a value the schema owns: a property, or a constructor parameter.
On a method, or on a parameter of anything else, the attribute carries its own `schema` or
`property` or the compiler reports it missing.

## Normalise on input, store the simple form

Where a property accepts both a rich input type and a plain serialized one, take both but
convert immediately in the constructor. Properties always hold the simple form. Enums, objects
and convenience types are input sugar only.

```php
// OA\Flow — FlowType accepted on input, stored as string
public function __construct(
    string|FlowType|null $flow = null,
    // ...
) {
    $this->flow = $flow instanceof \BackedEnum ? $flow->value : $flow;

    parent::__construct(x: $x, attachables: $attachables);
}
```

Augmenters, compilers and serialization then never need to branch on type.

### The parent constructor runs last

`AbstractAttribute`'s constructor records which public fields hold something other than a
default, and `isAuthored()` reads that record. A field filled in later, by an augmenter or
anything else, is not authored. The compilers use this to warn only about what the author
wrote, for example a keyword dropped beside a `$ref`.

The record is taken when the parent constructor runs. Promoted parameters are assigned before
the constructor body, so they are always in it. A field the body assigns itself, such as a
normalized `flow` or `component`, is in it only if the assignment comes first. Every
constructor in `src/Spec` therefore calls `parent::__construct()` as its last statement.
`AuthoredTest` builds each attribute class with every argument set and fails on any field
the record misses.

## The model speaks OpenAPI 3.1, the compilers translate

Attributes, their enums and the stored fields follow OpenAPI 3.1. Where another version writes
the same thing differently, the compiler for that version translates it. The model has no
per-version fields, and no enum case that exists for one version only. Version differences
then live in one place.

`OpenApi30Compiler` does most of the translating. `const` becomes a one-value `enum`, a type
list containing `null` becomes `nullable: true`, and `contentEncoding: base64` becomes
`format: byte`. Its class docblock lists every difference it handles.

`Format` follows the same rule. It has no `Byte` or `Binary` case, because 3.1 writes those as
`contentEncoding` and `contentMediaType`.

## `null` means unset, except where `null` is a value

A nullable property defaults to `null`, and `filter()` in the compilers drops it. That
breaks down for `mixed` properties: `null` is a legal example, default or const, so it
cannot also mean "not set". Those default to `Undefined::UNDEFINED` instead.

```php
// OA\Schema — null is a valid default, so the sentinel has to be something else
public mixed $default = Undefined::UNDEFINED,
```

`filter()` drops `null`, `Undefined::UNDEFINED` and `[]` alike, so a compiler that only needs
"omit when unset" gets that behaviour without doing anything. Emitting an explicit `null` or
`[]`, both legal values, takes a branch outside the `filter()` call. `compileSchema()` does
that for `default`, `const` and `example`, and `compileExample()`/`compileLink()` for `value`
and `requestBody`.

Classic uses the same sentinel (`Generator::UNDEFINED` is an alias of `Undefined::UNDEFINED`),
so `HybridBridge` passes these values straight through rather than translating them.

### An inferred field needs the sentinel too

There is a second reason to reach for `Undefined::UNDEFINED`, and it decides which nullable
properties get it. `Undefined::isDefault()` is true only for the sentinel, never for `null`,
so an augmenter that fills a field in guards on it:

```php
// Augmenter\Docblocks
if (!Undefined::isDefault($schema->description)) {
    return;
}
```

Writing `description: null` therefore means **no description**, and the docblock is left alone.
Leaving it out means "infer one". The attribute always wins. Classic behaves the same way. A
field nothing infers keeps a plain `null` default, since there is nothing to suppress.

That is what separates the two sets. `summary` and `description` on the operations, parameters
and schemas default to the sentinel because `Docblocks` and `EnumDescriptions` fill them. The
rest of the nullable properties do not.

**Adding inference for a field means changing its default.** Guarding a field that still
defaults to `null` makes the guard true for every attribute, and the inference silently never
runs. `DocblocksTest` pins both halves: that an absent value is inferred, and that an explicit
`null` suppresses it.

## Why resolution is its own step

Resolving unknown classes inside the augmenter pipeline would create an ordering problem: an
augmenter that adds schemas runs after `Names` and `Types`, so it would have to re-invoke
them on whatever it added. An augmenter that adds schemas has to run before them instead, as
the ones in [Variants are made where their use is known](#variants-are-made-where-their-use-is-known)
do. Running resolution between assembly and augmentation means every
schema exists before the augmenters start their single pass.

Discovery draws on two sources, both readable before any augmenter has run:

- **Ref values**: raw FQCN strings in `$ref`, not yet rewritten to `#/components/...`, so no
  dependency on `Names` or `Refs`. A `Schema\Ref` adds its qualifiers to the key
- **Reflector types**: non-builtin types of a schema's own properties, and of those its class's
  traits add, that carry a property attribute and no type yet, the ones `Types` will turn into a
  `$ref`. They are read straight off `\ReflectionProperty::getType()`, so no dependency on
  `Types`

A `ComponentIndex` deduplicates against what the specification already holds.

Resolution then runs as a convergence loop: discover, hand each key to the chain until one
claims it, re-discover. A key with qualifiers only goes to a `KeyResolverInterface`. A resolver
that knows the class alone would describe the plain class and claim the key for it. That is what handles transitive references. Resolving class A can
introduce a schema referencing class B, which the next pass picks up.

## Keys identify components, names come last

Until `Augmenter\Names` runs, a component has no name to be found by. What identifies it is its
`Specification\ComponentKey`: the class it describes, plus qualifiers that tell several
descriptions of one class apart. A component declared on a class has the plain key, the class
alone. `getComponentKey()` falls back to that, and `setComponentKey()` gives a component another.

Everything that looks a component up before the names exist does it by key, through
`ComponentIndex::findByKey()`, `findAllByKey()` and `hasKey()`. That covers the resolver chain,
`Types` deciding whether a class type can be referenced, and `Refs` rewriting a class-name
`$ref`. A `Schema\Ref` adds its qualifiers to the key it looks up.

Names are set once, by `Names`, through the namer the builder holds. A name can be asked for
before it is set and again afterwards, so `ComponentNamerInterface` requires the answer to
depend on the component alone. `ComponentNamer\ShortName` builds it from the class's short name
and adds the key's qualifiers, replacing what OpenAPI does not allow in a name. For a copy
`Generics` makes, it reads the explicit names `Generics` left on it as meta.

Qualifiers are the vocabulary of whoever makes the variant, with one exception.
`ComponentKey::TYPE_ARGUMENTS`, `of`, is swagger-php's own, and holds a generic class's type
arguments as one string, in one spelling: `Type\TypeArguments` writes it from the types, and
`Schema\Ref::getRefKey()` puts a hand-written one into the same form, so a ref and an inferred
type with the same arguments make the same key.

## Variants are made where their use is known

A component for a qualified key is made when something asks for it, in one of two places,
depending on when the use becomes visible.

- **The resolver chain**, before augmentation, for keys that a ref or a native property type
  already shows. A key with qualifiers goes to a `KeyResolverInterface` only, as
  [Why resolution is its own step](#why-resolution-is-its-own-step) describes.
- **An augmenter in `Resolve`, before `Names`**, for keys that only a docblock type shows. Types
  are read during augmentation, after the chain has finished, so the chain never sees them.
  `Augmenter\ImpliedSchemas` adds a schema for an enum used as a type. `Augmenter\Generics`
  runs after it, and after `Shortcuts`, so its dry run of `Types` sees the enum schemas and plain
  array schemas `Types` will see. It adds a copy of a generic class's schema for each set of type
  arguments, and runs `ImpliedSchemas` again after each round of copies, for an enum only a
  copy's arguments name.

Both augmenters run before `Names` and `Types`. What they add is named and typed in the same
single pass as everything else, which is the ordering problem the resolver step avoids for
classes.

## How type arguments flow

A member's type is resolved in one place, `TypeResolver::resolveIn()`, as seen from the component
it belongs to. Callers pass the component's key and hold no bindings themselves. `resolve()`, which
takes no key, keeps its signature from before generics, and `resolveIn()` falls back to it wherever
the key binds nothing, so an override of `resolve()` still applies. `Generics`,
`ImpliedSchemas`, `Types` and `Properties` all ask the one resolver the builder shares, so each
member is read once per component.

```text
 scan, collect, resolve      the generic class gets its ordinary schema
          │                  nothing reads the template yet
          ▼
 implied schemas             an enum used as a type gets its schema
          ▼
 make the copies             a use typed with arguments gives a key: class + arguments
          │                  no schema under that key → copy the class's schema
          │ ↺                enum schemas for the copies, then each copy's members read
          │                  in the copy, to find nested uses, until none is left
          ▼
 names                       from the key, and the names the copy carries
          ▼
 fill the types              ★ each member read in its component: templates bound
          │                  ★ each use points at the copy for its arguments
          ▼
 resolve refs                by key, as for any qualified reference
          ▼
 clean up, compile           the plain schema goes when nothing references it
```

1. **Reading a member in a component.** Given a key with `of`, the resolver pairs the class's
   `@template` names, in declaration order, with the arguments by position (one for each, or it
   binds nothing, as `Generics` makes no copy then), and rebuilds the
   docblock type with every template replaced, through nullable, union, intersection,
   collection, array shape and nested generic types. A template is declared `mixed` in PHP, and
   that `mixed` no longer adds `null` once a template is bound. Only a member the key's class
   declares itself binds, which the attribute factory's `SourceInspectorInterface::declares()`
   answers. An inherited member or one from a trait keeps its templates, with a warning. Answers
   are cached by member and component, and a member that binds nothing shares the plain answer.
2. **Keeping the arguments.** Where a type is a `GenericType` over a class that declares
   `@template`, the resolver keeps its arguments on the `SchemaType` as `Type\TypeArguments`,
   unless they are all `mixed` or one is still a template, as in the class's own `Node<T>`.
   `SchemaType::componentKey()` turns that into a key with `of`.
3. **Making the copies.** `Generics` asks `Types::wantedKeys()` which keys each typed attribute
   from `Walker::eachTypedAttribute()` would reference, read in its component, and adds every
   `Schema\Ref` qualified with `of`. `wantedKeys()` runs `Types` on a copy of the attribute, so the
   rules on which uses are referenced are the ones `Types` applies, not a second list. It does so
   only where the resolved type has type arguments somewhere (`SchemaType::hasTypeArguments()`), so
   a document without generics pays for the walk and nothing else. For each
   key the index does not hold, `Generics` copies the schema filed under the key without `of`,
   which is the plain schema unless the key has other qualifiers, and gives the copy the key. One
   whose arguments bind no template, or whose number of arguments differs from the class's
   templates, is reported and left alone. The explicit names the copy is built from go into its
   meta under `Generics::class`: the copied schema's own, and those of the argument classes'
   schemas. `component` is cleared so `Names` names it. A copy's own members are then read in the
   copy, which is how `Envelope<Envelope<Customer>>` asks for `Envelope<Customer>`. Only the
   copies a round made can ask for more, so later rounds read those and not the whole document.
   The loop stops at a depth of ten.
4. **Naming.** `ShortName` reads the key's `of` value back into types, and `TypeArgumentNames`
   writes them with a grammar of tokens: `-of.` opens a list, `_` separates arguments, `.-` closes
   a list where more follows, `.or.` and `.and.` join union and intersection members, an array
   shape is `shape` with `.is.` or `.opt.` between each key and its type and a last `etc` where it
   is unsealed, and `-h`, `-d` and `-u` escape `-`, `.` and `_` inside a value, and `-x` with two
   hex digits any other byte a name does not allow. A type keyword with hyphens is written in
   camel case, and `class-string<X>` as `classString` with `X` as its argument. Each class in the
   arguments is named as the namer names a class, or by the explicit name `Generics` left for it.
   A name can be parsed back into the arguments' structure, though nothing does so yet. Explicit
   names reach the copy only through meta, since a namer sees only the component it names.
5. **Filling the types.** `Types` reads each typed attribute in its component. At a use site,
   `Types::refTo()` sets a `Schema\Ref` with the key when a copy is filed under it, and the
   plain class otherwise. `Properties` reads a `mixed` member of a copy the same way to decide
   whether it is required.
6. **Resolving refs.** `Refs` finds the copy by key, as for any `Schema\Ref` with qualifiers. A
   key with `of` and no copy resolves to the key without `of`, as at a use site.

A copy carries nothing about generics except its key. Every augmenter after `Types` treats it
as an ordinary schema.

## Meta keys in the core

`getMeta()` and `setMeta()` carry data for whoever writes it. A key that is the name of a
swagger-php class or interface belongs to swagger-php, and that class or interface documents
what it means. Core code reads or writes meta only on purpose:
`MetaTest::testNothingInTheCoreUsesMeta()` lists every file in `src/` that does, with the
reason for each in its docblock. A new use fails that test until it is added there.

## Augmenter ordering lives in one place

Registration order is `Builder::getDefaultAugmenters()`. The phase each augmenter runs in comes
from its own `group()`: `Resolve` → `Reduce` → `Augment`. Within a phase, execution follows
registration order.

Ordering that matters: `TypeAliases` runs first of all, so every augmenter after it sees
valid types and a schema `Inheritance` copies is translated, and reported, once.
`Inheritance` must run before `PathItems`, because inherited
operations have to exist before path prefixes are resolved. `Parameters` must run before
`Merge`, because a parameter that is only a `ref` has no name or location until it does, and
`Merge\Operations` keys parameters by both. The phases keep that order: `Parameters` is in
`Resolve`, and both `Merge` runs are later. A `ref` parameter added after the `Resolve` phase
is not given its identity, and the second `Merge` run keeps it beside an inline twin.
`Properties` must run after `Types`, because it checks a default against the type `Types` gives
a property, and names a required property by the name `Types` fills in.
`SecurityRequirements` must run before `PathItems` too, because `PathItems` clones a path
item's requirements onto its operations, and reads each one as a `Security\Requirement`.
