# 🧪 Spec Pipeline Architecture

An overview of how the spec attributes pipeline turns your source code into an OpenAPI
document.

For the internals — how nesting is resolved, why the DTOs are shaped the way they are —
see [Spec pipeline internals](/dev/pipeline).

## Pipeline overview

```
Source files → Assembler → Specification → Resolver → Augmenters → Compiler → OpenAPI document
```

1. **Assembler** — scans source files, instantiates attributes from reflection, resolves nesting via slot maps (merge + hierarchical absorb)
2. **Specification** — a flat, typed container holding all collected attributes in buckets
3. **Resolver** — discovers and resolves unresolved FQCNs (e.g. unannotated model classes) before augmentation
4. **Augmenters** — enrich the specification with inferred data (types, refs, tags, etc.) via a grouped pipeline
5. **Compiler** — transforms the specification into a versioned OpenAPI document array
6. **Builder** — the unified entry point that orchestrates the pipeline

## Assembler

The Assembler reads spec attributes off your classes, methods, properties and parameters,
and works out which ones belong inside which. An `#[OA\Response]` on a method ends up
inside that method's operation; an `#[OA\Property]` on a class property ends up inside the
class's schema.

Each attribute declares where it can go, rather than the Assembler hard-coding the rules.
That is what lets you introduce your own attributes and have them nest correctly. The
declaration mechanism is described in
[Spec pipeline internals](/dev/pipeline).

Whatever is left once nesting is resolved is added to the Specification.

## Specification

The Specification is a flat, typed container with one bucket per root attribute type. It holds all attributes collected by the Assembler, organized by type (schemas, operations, pathItems, tags, etc.).

Augmenters read from and write to the Specification's buckets. The container is deliberately simple — no tree structure, no parent pointers. Cross-bucket relationships are resolved by augmenters using reflectors.

## Resolver

The Resolver discovers FQCNs referenced by the specification that have no corresponding component (e.g. model classes used in `$ref` or typed properties that were never scanned) and delegates their handling to a chain of `ResolverInterface` implementations. `Resolver\Reflection` is registered by default; further resolvers can be added, and the chain can be cleared entirely.

### Why a separate step

Solving this inside the augmenter pipeline creates ordering problems: an augmenter that adds new schemas runs after `Names` and `Types`, so it must re-invoke those augmenters on the newly added entries. The Resolver runs after assembly but before augmenters, so all schemas are present when augmenters start their single pass.

### Discovery

Two sources are inspected to find unresolved FQCNs:

- **Ref values** — walks all `$ref` attributes for raw FQCN strings (not yet rewritten to `#/components/...` paths). These are discoverable without `Names` or `Refs` having run.
- **Reflector types** — walks schema class reflectors for non-builtin typed properties and constructor parameters. This reads `\ReflectionProperty::getType()` directly, no `Types` augmenter needed.

A `ComponentIndex` is used to deduplicate against components already present in the specification.

### Resolution loop

The Resolver iterates in a convergence loop: discover unresolved FQCNs, pass each to the resolver chain (first resolver to claim success wins), then re-discover. This handles transitive references — resolving class A may add a schema referencing class B, which the next iteration discovers.

### ResolverInterface

```php
namespace OpenApi\Contracts;

interface ResolverInterface
{
    public function resolve(string $fqcn, Assembler $assembler): bool;
}
```

Resolvers receive the `Assembler` that assembled the specification. Collecting a reflector with it adds the result straight into the specification being built; resolvers that assemble differently can use their own assembler and add to `$assembler->getSpecification()` directly. Returning `true` marks the FQCN as handled and stops the chain for it.

### Resolver\Reflection

The default resolver collects the referenced class with the assembler in use:

```php
class Reflection implements ResolverInterface
{
    public function resolve(string $fqcn, Assembler $assembler): bool
    {
        $assembler->collect(new \ReflectionClass($fqcn));

        return $assembler->getSpecification()->buildComponentIndex()->find($fqcn) !== null;
    }
}
```

Because it is registered by default, **listing all related classes as builder sources is optional** — a single controller is enough as long as everything it references carries spec attributes:

```php
$result = (new Builder())
    ->setMode(Mode::SPEC)
    ->addSource(new \ReflectionClass(ProductController::class))
    ->build();
```

Classes without spec attributes add nothing, so `resolve()` returns `false` and the next resolver in the chain gets a turn — that is where, for example, a resolver generating schemas for unannotated classes would slot in.

### Configuring resolvers

The default chain contains `Resolver\Reflection` only. Add to it, or place a resolver ahead of it, via `withResolvers()`:

```php
use OpenApi\Resolver;
use OpenApi\Utils\TypedList;

$builder->withResolver(function (Resolver $resolver) {
    $resolver->withResolvers(fn (TypedList $resolvers) => $resolvers->add(new MyResolver()));
});
```

To opt out of resolution altogether, replace the chain with an empty list — the step is skipped when no resolvers are registered:

```php
$builder->withResolver(fn (Resolver $resolver) => $resolver->setResolvers(new TypedList()));
```

## Augmenters

Augmenters form a grouped pipeline that enriches the Specification in three ordered phases:

### Pipeline phases

| Phase       | Purpose                                                       |
| ----------- | ------------------------------------------------------------- |
| **Resolve** | Infer data from PHP reflection and cross-bucket relationships |
| **Reduce**  | Filter or remove entries                                      |
| **Augment** | Add derived metadata                                          |

Each augmenter implements `PipeInterface` and receives the full Specification, and those
within a phase run in registration order. The [Augmenters reference](/reference/augmenters)
lists which augmenters belong to each phase, in the order they run.

### Configuring augmenters

```php
$builder->withAugmenters(function (\OpenApi\Utils\Pipeline $pipeline) {
    // Get a typed reference to configure
    $pipeline->get(Augmenter\OperationIds::class)?->setHash(true);

    // Enable/disable
    $pipeline->get(Augmenter\Cleanup::class)?->setEnabled(false);

    // Insert before another
    $pipeline->insert(new CustomAugmenter(), Augmenter\Inheritance::class);

    // Remove entirely
    $pipeline->remove(Augmenter\EnumDescriptions::class);
});
```

### Writing a custom augmenter

A custom augmenter implements `PipeInterface`:

```php
use OpenApi\Utils\PipeInterface;
use OpenApi\Specification;
use OpenApi\Spec as OA;

class CustomAugmenter implements PipeInterface
{
    public function group(): string|\BackedEnum
    {
        return \OpenApi\Augmenter\Group::Augment;
    }

    public function __invoke(mixed $payload): mixed
    {
        foreach ($payload->schemas as $schema) {
            // enrich schemas...
        }

        // or

        // the walker will walk all attributes (including nested) of the specification
        $payload->getWalker()->visit(OA\Property::class, function (OA\Property $property) {
            // ...
        });

        // or walk all attributes with $ref set
        $payload->getWalker()->eachRef(function () {
            // $attribute->ref = ...
        });

        return $payload;
    }
}
```

## Compilers

Each OpenAPI version has its own compiler that handles version-specific output differences:

| Compiler            | Version | Key differences                                                   |
| ------------------- | ------- | ----------------------------------------------------------------- |
| `OpenApi30Compiler` | 3.0.x   | `nullable` as property, `exclusiveMinimum` as boolean             |
| `OpenApi31Compiler` | 3.1.x   | `nullable` via type array, `exclusiveMinimum` as number, webhooks |
| `OpenApi32Compiler` | 3.2.x   | Extends 3.1 (currently without additional features)               |

The compiler transforms a Specification into a plain PHP array representing the OpenAPI document. Version selection is automatic based on `Builder::setVersion()` or the `#[OA\OpenApi(version: '...')]` attribute.

## Classic processor mapping

How each classic processor maps to the new pipeline:

| Classic Processor     | Spec Equivalent                          | Stage              |
| --------------------- | ---------------------------------------- | ------------------ |
| ExpandClasses         | `Inheritance` + Assembler                | augment + assembly |
| ExpandTraits          | `Inheritance` + Assembler                | augment + assembly |
| ExpandInterfaces      | `Inheritance` + Assembler                | augment + assembly |
| ExpandEnums           | `Enums`                                  | augment            |
| MergeIntoOpenApi      | `Assembler`                              | assembly           |
| MergeIntoComponents   | Compiler                                 | compile            |
| MergeJsonContent      | `Shortcuts`                              | resolve            |
| MergeXmlContent       | `Shortcuts`                              | resolve            |
| BuildPaths            | Compiler                                 | compile            |
| AugmentSchemas        | `Names` + `Types` + Assembler + Compiler | mixed              |
| AugmentProperties     | `Types`                                  | resolve            |
| AugmentParameters     | `Types`                                  | resolve            |
| AugmentItems          | `Types`                                  | resolve            |
| AugmentRequestBody    | `Types`                                  | resolve            |
| AugmentRefs           | `Refs`                                   | resolve            |
| AugmentDiscriminators | `Refs`                                   | resolve            |
| AugmentTags           | `Tags`                                   | augment            |
| AugmentMediaType      | `MediaTypes`                             | augment            |
| DocBlockDescriptions  | `Docblocks`                              | augment            |
| OperationId           | `OperationIds`                           | augment            |
| CleanUnmerged         | Assembler (orphan validation)            | assembly           |
| CleanUnusedComponents | `Cleanup`                                | reduce             |
| PathFilter            | `PathFilter`                             | reduce             |

The key architectural difference: classic processors walk a single nested annotation tree in one chain. Spec augmenters operate on a flat Specification of typed buckets, grouped into explicit phases. Both mutate their attributes in place — the pipelines differ in shape and ordering, not in mutability.
