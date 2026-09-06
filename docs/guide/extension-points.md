# Extension points 🧪

The spec pipeline is assembled from parts that can be replaced or added to. Between them
they cover feeding it from somewhere other than a directory scan, accepting attributes it
does not know, and deriving fields nobody wrote by hand.

They are described here roughly in the order the pipeline reaches them.

## Translators

A translator runs during assembly, once per reflector. `getAttributes()` says which raw
attributes to read off it; `translate()` returns what the assembler should treat as declared.

Given a framework attribute that knows nothing about swagger-php:

<<< @/snippets/guide/extension-points/route_attribute.php

a translator turns it into a spec attribute:

<<< @/snippets/guide/extension-points/route_translator.php

Turning a foreign attribute into a spec one is a use, not the use. The other is adding a
native attribute nobody wrote, which is how swagger-php uses the mechanism itself — both
`DefaultAttributeTranslator` and `OptionalPropertyAttributeTranslator` ship by default. The
[extension points reference](/reference/extension-points) says what each does.

`translate()` sees `$created`, what this translator read on this pass, and `$attributes`,
what earlier translators already resolved. It can add to either, replace them, or leave them
alone. Ordering follows registration.

Translators are registered on the attribute factory, so `withTranslators()` nests inside
`withAttributeFactory()`:

```php
$builder->withAttributeFactory(fn (AttributeFactory $factory) => $factory->withTranslators(
    fn (TypedList $translators) => $translators->add(new RouteTranslator())
));
```

## Subclassing a spec attribute

Spec attributes are not `final`. A subclass can derive its own constructor arguments, usually
by reflecting over the class it is given, which keeps the repetition out of the annotated
code:

<<< @/snippets/guide/extension-points/dto_attribute.php

`#[Dto(of: Pet::class)]` then names the schema and marks every public property required,
without either being written out. The subclass is an `OA\Schema` as far as the rest of the
pipeline is concerned, so merging, containment and compilation treat it the same.

## Resolvers

Seeding from reflectors means the specification can name a class that was never a source: a
controller is added, one of its `$ref`s points at a DTO, and nothing ever collected the DTO.
Each such name goes to the resolver step.

`Resolver\Reflection` is registered by default and covers the ordinary case — the class
exists and carries spec attributes, so it is collected by reflection and the reference
resolves. It returns `false` when the class yielded no component, and an unresolved reference
is reported rather than silently dropped.

`Builder::withResolver()` hands the resolver list to a callable. Resolvers are tried in
order and the first success wins, so one added after the default is
handed the classes the default could not resolve. What it makes of them is up to whoever
writes it — deriving a schema from a class's public properties and PHP types is one option,
and would let a DTO carrying no spec attributes appear in the document. `insert()` places a
resolver ahead of the default rather than after it.

## Augmenters

An augmenter runs after assembly, over the whole `Specification`, and fills in what the
attributes left out:

<<< @/snippets/guide/extension-points/tag_augmenter.php

It does not have to implement anything. Any callable taking the `Specification` and returning
it will do; `PipeInterface` exists to declare a phase, and a pipe without it lands in the
pipeline's default group:

```php
$pipeline->add(fn (Specification $spec) => $spec);
```

`Builder::withAugmenters()` hands the pipeline to a callable, which adds, replaces or
removes. Phases run **resolve** → **reduce** → **augment**, and within a phase in
registration order.
The enum is `OpenApi\Augmenter\Group`. The [Augmenters
reference](/reference/augmenters) lists the built-in pipeline and what each phase is for.

## Compilers

`Builder::setCompiler()` replaces the compiler that turns the `Specification` into a
document. One is resolved from the target version otherwise, so this is for output a shipped
compiler does not produce.

## The classic escape hatch

`Builder::withGenerator()` configures the classic `Generator` — the whole pipeline in
classic mode, and the scanning pass in hybrid. The callable receives a default `Generator`
and may configure it in place or return another. Spec mode has no `Generator`, so the hook
is never called there.

## Putting it together

`RouteTranslator` and `TagFromController` from above, wired onto a builder seeded from a
reflector rather than a directory:

<<< @/snippets/guide/extension-points/wiring.php

Given a controller carrying `#[Route(path: '/pets')]` and an `#[OA\Response]`, that produces:

<<< @/snippets/guide/extension-points/wiring-3.1.0.yaml

## Where the boundaries are

Some things are deliberately not extension points. Each has a reason, and each has an
alternative.

**Property types are not widened for downstream convenience.** The strong typing is what
makes the DTOs worth having. Metadata that only means something to one integration belongs
in an `Attachable`, not in a widened `$ref: string|object`.

**There is no framework-specific code, and no plans for any.** Translators, augmenters and
attachables are the contract; anything a framework needs can be built from them, outside
this repository.

**There are no events or listeners.** The pipeline is deterministic and reads top to bottom,
which is what makes a wrong document traceable to the step that produced it. Event ordering
is not obvious from reading, and it is harder to test.

**Assembler internals stay internal.** `collect()` is the contract. How it resolves nesting
is free to change, and has.

## Going further

- [Builder reference](/reference/builder) — every hook on its own, with signatures
- [Augmenters reference](/reference/augmenters) — the built-in pipeline and its phases
- [Architecture](/reference/architecture) — how the stages fit together
- [Spec pipeline internals](/dev/pipeline) — slot maps, resolution, and the rules a new
  attribute has to follow
