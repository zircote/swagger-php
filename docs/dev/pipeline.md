# Spec pipeline internals

Things about the spec pipeline that are easy to get wrong from reading the source, and that
have caused incorrect documentation before. For the user-facing description see
[Architecture](/reference/architecture); this page is for people changing the pipeline.

## Where new code goes

`src/Annotations/` and `src/Attributes/` are the classic pipeline. They are maintained but
effectively closed to new features — see [ROADMAP](https://github.com/zircote/swagger-php/blob/master/ROADMAP.md)
for the v7/v8 plan. New spec work belongs in `src/Spec/`, `src/Augmenter/` and
`src/Compiler/`.

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

Do not hand-maintain a class tree in documentation. The generated
[Spec Attributes reference](/reference/spec-attributes) lists every attribute with its
parameters and what it can nest into, and cannot go stale.

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
