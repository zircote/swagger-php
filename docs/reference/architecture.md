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

Between assembly and augmentation, the Resolver looks for classes the specification refers
to but does not contain — a model used in a `$ref`, or the type of a property on a schema —
and hands each one to a chain of `ResolverInterface` implementations.

`Resolver\Reflection` is registered by default: it reflects the class and collects it with
the assembler already in use. A class carrying no spec attributes contributes nothing, so
resolution reports failure and the next resolver in the chain gets a turn — which is where
something generating schemas for unannotated classes would slot in.

Wiring resolvers into a build is covered in
[Resolver configuration](/reference/builder#resolver); discovery and the convergence loop
are in [Spec pipeline internals](/dev/pipeline).

### Writing a resolver

```php
namespace OpenApi\Contracts;

interface ResolverInterface
{
    public function resolve(string $fqcn, Assembler $assembler): bool;
}
```

Resolvers are handed the `Assembler` that built the specification, so collecting a reflector
with it adds the result straight into the specification in progress. A resolver that
assembles differently can add to `$assembler->getSpecification()` directly. Return `true` to
mark the FQCN handled and stop the chain for it.

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

### Declaring configuration

Mark a constructor parameter with `#[Config]` to make it settable via `-D`/`-c` and via
[`withAugmenters()`](/reference/builder#augmenters), and to have it appear in the
[generated reference docs](/reference/augmenters). It needs a matching `set*()` method —
that's what `-c` and `Pipeline::configure()` call.

```php
use OpenApi\Utils\Config;
use OpenApi\Utils\PipeInterface;

class CustomAugmenter implements PipeInterface
{
    public function __construct(
        #[Config('Whether the custom rule is applied.')]
        protected bool $enabled = true,
    ) {
    }

    public function setEnabled(bool $enabled): static
    {
        $this->enabled = $enabled;

        return $this;
    }

    // ...
}
```

Parameters without `#[Config]` are collaborators, not settings — `Pipeline::getConfig()`
(what `-D` prints) won't report them.

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
