# Extension points

The spec pipeline is assembled from parts that can be replaced or added to. Between them
they cover feeding it from somewhere other than a directory scan, accepting attributes it
does not know, and deriving fields nobody wrote by hand.

They are described here roughly in the order the pipeline reaches them.

## Translators

A translator runs during assembly, once per reflector. `getAttributes()` says which raw
attributes to read off it, and `translate()` returns what the assembler should treat as
declared.

Given a framework attribute that knows nothing about swagger-php:

<<< @/snippets/guide/extension-points/route_attribute.php

a translator turns it into a spec attribute:

<<< @/snippets/guide/extension-points/route_translator.php

Turning a foreign attribute into a spec one is a use, not the use. The other is adding a
native attribute nobody wrote, which is how swagger-php uses the mechanism itself. Both
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

## Contributing to the Specification

`Builder::withSpecification()` hands the assembled `Specification` to a callable before the
resolver runs. Add attributes to it and they go through the rest of the pipeline as scanned ones do:

<<< @/snippets/guide/extension-points/contribution.php

That produces:

<<< @/snippets/guide/extension-points/contribution-3.1.0.yaml

The pipeline reads a reflector, not a source file, so where an attribute came from does not
matter. A contribution that carries one is treated as the assembler's own: an operation given
its `ReflectionMethod` takes summary, description and operation id from the method, and a
parameter given its `ReflectionParameter` takes its name, type and whether it is required. A
router that knows `[UserController::class, 'index']` for a route can hand all of that over.
What has no reflector anywhere, an entity registry or a serializer's configuration, goes in
bare, and this is the only way it gets in.

An augmenter can also `add()` attributes, but it runs after resolution, so a `$ref` inside
what it adds stays unresolved. Use an augmenter to enrich what is there, and this to put
something there.

Once added, a contribution looks like any scanned attribute. If a later step needs to tell
yours apart, such as a [merger](#mergers) deciding precedence or an augmenter that should leave
them alone, mark them as you add them with `setMeta()` under a key you own. Read it back with
`getMeta()` there. Keys that are the name of a swagger-php class belong to swagger-php.

## Resolvers

Seeding from reflectors means the specification can name a class that was never a source: a
controller is added, one of its `$ref`s points at a DTO, and nothing ever collected the DTO.
Each such name goes to the resolver step.

`Resolver\Reflection` is registered by default and covers the ordinary case. The class exists
and carries spec attributes, so it is collected by reflection and the reference resolves. It
returns `false` when the class yielded no component, and an unresolved reference is reported
rather than silently dropped.

A resolver is asked about a key, the class plus any qualifiers. It implements
`KeyResolverInterface`, and `Resolver\Concerns\KeyResolverTrait` supplies the method for a class
name alone. [Describing a class more than once](#describing-a-class-more-than-once) has what a
key with qualifiers is for.

`Builder::withResolver()` hands the resolver list to a callable. Resolvers are tried in
order and the first success wins, so one added after the default is
handed the classes the default could not resolve. What it makes of them is up to whoever writes
it. Deriving a schema from a class's public properties and PHP types is one option,
and would let a DTO carrying no spec attributes appear in the document. `insert()` places a
resolver ahead of the default rather than after it.

A resolver implementing `LoggerAwareInterface` is given the logger set with
[`Builder::setLogger()`](/reference/builder#logger).

## Augmenters

An augmenter runs after assembly, over the whole `Specification`, and fills in what the
attributes left out:

<<< @/snippets/guide/extension-points/tag_augmenter.php

It does not have to implement anything. Any callable taking the `Specification` and returning
it will do. `PipeInterface` exists to declare a phase, and a pipe without it lands in the
pipeline's default group:

```php
$pipeline->add(fn (Specification $spec) => $spec);
```

`Specification` carries the views an augmenter needs over the assembled tree: `getWalker()`
for traversal, `buildComponentIndex()` to resolve a `$ref`, and `buildPathItemHierarchy()` for
the `PathItem`s governing a class. Path-level metadata has to be read through it, since a class
without its own `PathItem` is governed by its ancestors'.

`Builder::withAugmenters()` hands the pipeline to a callable, which adds, replaces or
removes. Phases run **resolve** → **reduce** → **augment**, and within a phase in
registration order.
The enum is `OpenApi\Augmenter\Group`. The [Augmenters
reference](/reference/augmenters) lists the built-in pipeline and what each phase is for.

## Component names

A component declared on a class without a name is, by default, named after the class, by its
short name. Two classes that share a short name, such as `App\Dto\User\CreateDTO` and
`App\Dto\Order\CreateDTO`, then claim one name. The document keeps one of them and logs a
warning.

The name comes from a component namer, set with `Builder::setComponentNamer()`.
`ComponentNamer\ShortName` is the default. `ComponentNamer\QualifiedName` uses the whole class
name:

```php
use OpenApi\ComponentNamer\QualifiedName;

$builder->setComponentNamer(new QualifiedName(strip: 'App\Dto\\'));
```

The two classes above become `User.CreateDTO` and `Order.CreateDTO`. `strip` removes a
namespace prefix first, and `separator` joins what is left. It defaults to `.`, and an empty
string runs the segments together as `UserCreateDTO`. A component with an explicit name keeps
it.

A namer of your own implements `OpenApi\Contracts\ComponentNamerInterface`. Its `name()`
receives the component and returns its name, or `null` to leave the component inline. It can
be asked about one component more than once, before its name is set and after, so the answer
depends on the component alone. Extending `ShortName` and overriding `className()` changes how
a class is named and keeps the names of an `#[OA\Components]` holder's members.

On the command line, `--component-namer` picks a namer by class: `QualifiedName` for a built-in
one, or the fully qualified name of your own, loaded with `--bootstrap`. It is built without
arguments, so other settings need a subclass.

::: info Mode difference
The namer names components in spec and hybrid mode. Classic mode does not use it.
:::

## Describing a class more than once

A class name stands for one component. A `$ref` given as the class, and a property typed with
it, point at the component declared on that class. A producer can describe one class more than
once, for example once per serialization group. Each description is a component with a name of
its own, and a key tells them apart.

A key is the class plus qualifiers. A component declared on a class has the plain key, the class
alone, unless `setComponentKey()` gives it another. Each further description gets a
`ComponentKey` with qualifiers, and a `Schema\Ref` with the same qualifiers references it:

<<< @/snippets/guide/extension-points/descriptions.php

That produces:

<<< @/snippets/guide/extension-points/descriptions-3.1.0.yaml

`author` is typed with the class, so it references the plain key, `Author`. `reviewer` asks for
the `summary` group and gets `AuthorSummary`.

Qualifiers map a name to a scalar or a list of scalars, one level deep. Their order does not
matter, and a list is compared as a set, so `['groups' => ['summary', 'admin']]` and
`['groups' => ['admin', 'summary']]` are one key. A value whose order matters, such as type
arguments, goes in as one string.

A key names one component per bucket. Where two components share a key, a `$ref` given as that
key resolves to neither and logs a warning naming both. A property typed with the class is left
without a type, with a warning. Give all but one of them qualifiers, or reference the one you
mean by its name, `#/components/schemas/AuthorSummary`.

In an augmenter, `ComponentIndex::findByKey()` returns the component a key names in a bucket,
and `findAllByKey()` returns every one.

A description can also be made when something asks for it. A `Schema\Ref` with qualifiers that
no component has goes to the [resolvers](#resolvers), with its key. A resolver that knows the
class's groups adds a schema for them, with that key, and the reference finds it.
`Resolver\Reflection` describes a class once, from its own attributes, so it leaves a key with
qualifiers to the next resolver.

::: info Mode difference
Keys are read in spec and hybrid mode. Classic mode does not use them.
:::

## Mergers

Two attributes can claim one key. Two operations on the same path and method, or two schemas
named `Pet`: a scan finds one, a `withSpecification()` hook contributes the other, an
inheritance clone makes a third. Something has to decide which of them the document holds.
Until it does, the compiler decides by accident. It writes each into a PHP array and keeps
whichever it wrote last.

`Augmenter\Merge` decides instead, through a chain of mergers. A merger says what makes two
attributes the same one, and what the survivor is. Below, an operation the scan already
described keeps the key against one a hook contributed and marked as its own:

```php
use OpenApi\Contracts\AttributeInterface;
use OpenApi\Contracts\MergerInterface;
use OpenApi\Spec as OA;

final class MyOperationMerger implements MergerInterface
{
    public function supports(string $class): bool
    {
        return is_a($class, OA\Operation::class, true);
    }

    public function identity(AttributeInterface $attribute): ?string
    {
        return $attribute->path !== null && $attribute->method !== null
            ? $attribute->method . ' ' . $attribute->path
            : null;
    }

    public function merge(AttributeInterface $earlier, AttributeInterface $later): AttributeInterface
    {
        return $later->getMeta(self::class, false) ? $earlier : $later;
    }
}
```

`Builder::withMergers()` registers it. They are tried in order and the first to claim a type
handles it. `Merge\LastWins` claims everything, keeps the later entry and warns with both
locations, so it ships last:

```php
use OpenApi\Merge;
use OpenApi\Utils\TypedList;

$builder->withMergers(fn (TypedList $mergers) => $mergers->insert(
    new MyOperationMerger(),
    Merge\LastWins::class,
));
```

`identity()` returning `null` means the attribute never merges and passes through: that is how
servers and security requirements stay as they are, being positional rather than keyed.

`$earlier` and `$later` are in producer order, which is the only thing the pipeline guarantees,
so `return $later` is last-wins. Precedence beyond that order is a policy the core does not
hold. A package that needs to recognise its own attributes marks them as it creates them, with
`$operation->setMeta(MyOperationMerger::class, true)`, and reads that back in `merge()`. `meta`
is keyed by whoever writes to it. Nothing in swagger-php writes or reads it.

To combine two operations for the same path and method instead of choosing between them,
register the shipped `Merge\Operations` the same way, ahead of `Merge\LastWins`. It is not on
by default. It fills each field one of them leaves unset from the other, so a contributed route
and an attribute adding responses to it become one operation. What both set is decided by a
`Merge\Mode`: fold into the later (the default), into the earlier, or take only what does not
overlap. A callable can choose the mode per pair, which is how a producer's mark decides
precedence without a merger of its own:

```php
use OpenApi\Contracts\AttributeInterface;
use OpenApi\Merge;

new Merge\Operations(
    fn (AttributeInterface $earlier, AttributeInterface $later): Merge\Mode
        => $later->getMeta(MyProducer::class) ? Merge\Mode::First : Merge\Mode::Last,
);
```

A parameter that is only a `ref` folds with an inline one of the same name and location, since
the `Parameters` augmenter gives it the name and location of its component first. The reference
is what survives.

See the [Mergers reference](/reference/mergers) for how each field folds.

The pass runs over the `Specification`'s own collections, where attributes sharing a key come
from different places. A duplicate key *inside* one attribute, such as two `200` responses in
one operation, is two entries one author wrote in one place, and the compiler is left to keep
the last of them.

## Compilers

`Builder::setCompiler()` replaces the compiler that turns the `Specification` into a
document. One is resolved from the target version otherwise, so this is for output a shipped
compiler does not produce.

## The classic escape hatch

`Builder::withGenerator()` configures the classic `Generator`, which is the whole pipeline in
classic mode and the scanning pass in hybrid. The callable receives a default `Generator`
and may configure it in place or return another. Spec mode has no `Generator`, so the hook
is never called there.

In hybrid, the `Generator` arrives with an empty processor pipeline, so a processor added
here is the only one that runs, and it sees the annotation instances as scanned: properties are not
yet attached to their schemas, and inheritance has not yet produced any `allOf`. The bridge
and the augmenters do that work afterwards. A processor that only reads or changes what the
author wrote works in hybrid. One that depends on that structure belongs in an augmenter.

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
in an `Attachable` when it is declared in source next to the attribute it describes, and in
`setMeta()` when code attaches it along the way. It does not belong in a widened `$ref:
string|object`.
Neither reaches the generated document.

**There is no framework-specific code, and no plans for any.** Translators, contributions,
augmenters and attachables are the contract. Anything a framework needs can be built from them,
outside this repository.

**There are no events or listeners.** The pipeline is deterministic and reads top to bottom,
which is what makes a wrong document traceable to the step that produced it. Event ordering
is not obvious from reading, and it is harder to test.

**Assembler internals stay internal.** `collect()` is the contract. How it resolves nesting
is free to change, and has.

## Going further

- [Builder reference](/reference/builder): every hook on its own, with signatures
- [Augmenters reference](/reference/augmenters): the built-in pipeline and its phases
- [Architecture](/reference/architecture): how the stages fit together
- [Spec pipeline internals](/dev/pipeline): slot maps, resolution, and the rules a new
  attribute has to follow
