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

Documentation has repeatedly claimed these objects are immutable. They are not; do not
reason about the pipeline as if nothing is being mutated.

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

A `[]` suffix appends to a collection; a bare name assigns a scalar. This is easy to read
backwards — the interface's own docblock had it inverted for a while.

Because the child declares the relationship, downstream code can extend the system: a
custom attachable names its own nesting targets without touching any native attribute.

### How resolution runs

Resolution is driven purely by these declarations — it reads nothing from PHP's own
structural semantics:

1. **Sibling merge** — attributes on the same reflector compose via `merge()`
2. **Hierarchical absorb** — what remains flows upward a level at a time via `contained()`,
   first match wins

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

All spec attributes extend `AbstractAttribute`. Typed subclasses such as `Operation\Get`
and `Parameter\Path` pre-fill fields the base class requires explicitly; the base class is
always usable directly for full control.

Do not hand-maintain a class tree in documentation. The generated
[Spec Attributes reference](/reference/spec-attributes) lists every attribute with its
parameters and what it can nest into, and cannot go stale.

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

This is what keeps the Assembler concerned only with nesting.

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

## Augmenter ordering lives in one place

Registration order is `Builder::getDefaultAugmenters()`. The phase each augmenter runs in
comes from its own `group()` — `Resolve` → `Reduce` → `Augment`. Within a phase, execution
follows registration order.

Ordering that matters: `Inheritance` must run before `PathItems`, because inherited
operations have to exist before path prefixes are resolved.

Prose that re-lists the order drifts. Point at the method instead.

## Typed operation subclasses drop `requestBody`

`Operation\Get`, `Operation\Head`, `Operation\Options` and `Operation\Trace` have no
`requestBody` constructor parameter. Passing one is a PHP
`Error: Unknown named parameter $requestBody`, raised when the attribute is instantiated —
not a validation warning. `Post`, `Put`, `Patch` and `Delete` accept it.
