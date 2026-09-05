# Spec pipeline internals

The parts of the spec pipeline that are easiest to get wrong when reading the source. For
how the pipeline fits together, see [Architecture](/reference/architecture).

## The DTOs are mutable

Spec attributes are plain data containers, but they are **not** immutable, and neither is
`Specification`. Augmenters assign to attribute properties throughout —
`Refs::mergeAllOf()` nulls `$schema->properties`, `Types` fills schema fields in place —
and `Specification` exposes public arrays that `add()` appends to.

What *is* true, and what distinguishes them from classic annotations, is that they carry no
serialization logic. Serialization is the compiler's job.

## Slot maps: the slot belongs to the parent

Nesting is declared by the child, via two methods on `AttributeInterface`:

- `merge()` — how this attribute composes into a **sibling** on the same reflector
- `contained()` — which **outer-level** attribute types can absorb it from an inner level

Both return `[TargetClass => 'slot']`, and in both cases the slot names a property on the
**target**, not on the attribute declaring it:

```php
// OA\Property
public function contained(): array
{
    return [Schema::class => 'properties[]'];   // `properties` lives on Schema
}
```

A `[]` suffix appends to a collection; a bare name assigns a scalar. It is easy to read
backwards.

The target's own type has to admit the class declaring the slot — `Schema` once named
`Schema::$properties`, a `list<Property>`, which crashed the inheritance augmenter as soon
as anything reached it. `SlotMapConsistencyTest` checks every slot against its target.

Because the child declares the relationship, downstream code can extend the system: a
custom attachable names its own nesting targets without touching any native attribute.

### How resolution runs

Resolution is driven purely by these declarations — it reads nothing from PHP's own
structural semantics:

1. **Sibling merge** — attributes on the same reflector compose via `merge()`
2. **Hierarchical absorb** — what remains flows upward a level at a time via `contained()`,
   first match wins

Sibling merge chains resolve inner-to-outer, not in declaration order: a `MediaType`
stacked with a `Response` and an `Operation` finds its `Response` before the `Response` is
folded into the `Operation`, whichever way the three are declared. Only attributes whose
types name each other as merge targets — possible with custom attachables, not among the
native attributes — cannot be ordered that way and resolve in declaration order instead.

If a level has containers, an unmatched non-root attribute is an error. If a level has no
containers at all, unmatched attributes pass through to the level above.

### What survives resolution

Only *root* attributes should remain, and those are what enters the Specification. A root
attribute is one that can stand alone — it owns a bucket and needs no parent.

- **Always root**: `Schema`, `Operation`, `PathItem`, `OpenApi`, `Info`, `Tag`, `Server`,
  `ExternalDocumentation`, `Security\Scheme`, `Components`, `Attachable`
- **Conditionally root**, when their own key is set and `ref` is not: `Response`
  (`response`), `Parameter` (`parameter`), `Link` (`link`); `RequestBody` needs `request`
  set but has no `ref` check
- **Never root**: `Header`, `Example`, `MediaType`, `Property` — these must nest inside a
  parent or sit in a `Components` container

Each attribute decides for itself, in `isRoot()`.

Root does not mean un-nested. `isRoot()` says what an attribute may be when nothing consumes
it; `merge()` and `contained()` run first, and often do. `Schema` is always root, and is
routinely merged into a sibling — a `Property` or a `MediaType`, say — instead of reaching
the Specification at all.

The user-facing version of this distinction is in
[Using Spec Attributes](/guide/spec-attributes#components); this list is the full one.

## Directory layout is not the class hierarchy

`src/Spec/` nests directories for readability, not inheritance. Notably:

- `Property extends AbstractAttribute` — **not** `Schema`. This is the deliberate change
  from classic (where `Annotations\Property extends Schema`) and it is what makes stacking
  `#[OA\Property]` and `#[OA\Schema]` on the same target work.
- `Encoding extends AbstractAttribute` — **not** `MediaType`, despite `Property\Encoded`.
- `Contact`, `License`, `ServerVariable` extend `AbstractAttribute`, not `Info`/`Server`.
- `OA\Security` is a namespace, not a class. The classes are `Security\Requirement` and
  `Security\Scheme`.

Genuinely nested: `Schema\{AdditionalProperties,Items,Ref}`, `Property\Encoded`,
`Operation\*`, `Parameter\*`, `MediaType\{Json,Xml}`, `Flow\*`, `Security\Scheme\*`.

The [Spec Attributes reference](/reference/spec-attributes) lists every attribute with its
parameters and what it can nest into.

## Reflectors are the glue

Every root DTO keeps the reflector it came from. This is how relationships that span
buckets get resolved after assembly, without the DTOs having to reference each other:

- **PathItem to Operation** — a PathItem sits on a class, operations on its methods; the
  `PathItems` augmenter uses `ReflectionMethod::getDeclaringClass()` to pair them
- **Prefix composition** — `ReflectionClass::getParentClass()` walks ancestors so parent
  PathItems can contribute path prefixes
- **OperationId generation** — class and method names come from the reflector
- **Type inference** — `Types` reads PHP type declarations off property and parameter
  reflectors
- **Naming** — a component schema takes its name from the class it sits on; a property
  takes its name from the property, parameter or constant it sits on

This is what keeps the Assembler concerned only with nesting.

A method reflector supplies no name — `getUnnamed()` is not the property `unnamed`, and the
pipeline does not guess at accessor prefixes. So a bare `Schema` becomes a `Property` only
where the reflector declares a value the schema owns: a property, or a constructor
parameter. On a method, or on a parameter of anything else, the attribute carries its own
`schema` or `property` or the compiler reports it missing.

## Normalise on input, store the simple form

Where a property accepts both a rich input type and a plain serialized one, take both but
convert immediately in the constructor. Properties always hold the simple form; enums,
objects and convenience types are input sugar only.

```php
// OA\Flow — FlowType accepted on input, stored as string
public function __construct(
    string|FlowType|null $flow = null,
    // ...
) {
    parent::__construct(x: $x, attachables: $attachables);
    $this->flow = $flow instanceof \BackedEnum ? $flow->value : $flow;
}
```

Augmenters, compilers and serialization then never need to branch on type.

## `null` means unset, except where `null` is a value

A nullable property defaults to `null`, and `filter()` in the compilers drops it. That
breaks down for `mixed` properties: `null` is a legal example, default or const, so it
cannot also mean "not set". Those default to `Undefined::UNDEFINED` instead.

```php
// OA\Schema — null is a valid default, so the sentinel has to be something else
public mixed $default = Undefined::UNDEFINED,
```

`filter()` drops `null`, `Undefined::UNDEFINED` and `[]` alike, so a compiler that only
needs "omit when unset" gets that behaviour without doing anything. Emitting an explicit
`null` or `[]` — both legal values — takes a branch outside the `filter()` call, which is
what `compileSchema()` does for `default`, `const` and `example`, and
`compileExample()`/`compileLink()` for `value` and `requestBody`.

Classic uses the same sentinel — `Generator::UNDEFINED` is an alias of
`Undefined::UNDEFINED` — so `HybridBridge` passes these values straight through rather than
translating them.

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

Writing `description: null` therefore means **no description**, and the docblock is left
alone; leaving it out means "infer one". The attribute always wins. Classic behaves the same
way. A field nothing infers keeps a plain `null` default — there is nothing to suppress.

That is what separates the two sets. `summary` and `description` on the operations,
parameters and schemas default to the sentinel because `Docblocks` and `EnumDescriptions`
fill them; the rest of the nullable properties do not.

**Adding inference for a field means changing its default.** Guarding a field that still
defaults to `null` makes the guard true for every attribute, and the inference silently never
runs. `DocblocksTest` pins both halves — that an absent value is inferred, and that
an explicit `null` suppresses it.

## Why resolution is its own step

Resolving unknown classes inside the augmenter pipeline would create an ordering problem: an
augmenter that adds schemas runs after `Names` and `Types`, so it would have to re-invoke
them on whatever it added. Running resolution between assembly and augmentation means every
schema exists before the augmenters start their single pass.

Discovery draws on two sources, both readable before any augmenter has run:

- **Ref values** — raw FQCN strings in `$ref`, not yet rewritten to `#/components/...`, so
  no dependency on `Names` or `Refs`
- **Reflector types** — non-builtin types on properties and constructor parameters, read
  straight off `\ReflectionProperty::getType()`, so no dependency on `Types`

A `ComponentIndex` deduplicates against what the specification already holds.

Resolution then runs as a convergence loop: discover, hand each FQCN to the chain until one
claims it, re-discover. That is what handles transitive references — resolving class A can
introduce a schema referencing class B, which the next pass picks up.

## Augmenter ordering lives in one place

Registration order is `Builder::getDefaultAugmenters()`. The phase each augmenter runs in
comes from its own `group()` — `Resolve` → `Reduce` → `Augment`. Within a phase, execution
follows registration order.

Ordering that matters: `Inheritance` must run before `PathItems`, because inherited
operations have to exist before path prefixes are resolved.
