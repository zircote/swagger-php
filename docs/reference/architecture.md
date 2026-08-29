# 🧪 Spec Pipeline Architecture

An overview of how the spec attributes pipeline turns your source code into an OpenAPI
document — enough context to configure it, extend it, or reason about its output.

For the internals — how nesting is resolved, why the DTOs are shaped the way they are —
see [Spec pipeline internals](/dev/pipeline), which is written for people changing the
pipeline rather than using it.

## Pipeline overview

```
Source files → Assembler → Specification → Augmenters → Compiler → OpenAPI document
```

1. **Assembler** — scans source files, instantiates attributes from reflection, resolves nesting via slot maps (merge + hierarchical absorb)
2. **Specification** — a flat, typed container holding all collected attributes in buckets
3. **Augmenters** — enrich the specification with inferred data (types, refs, tags, etc.) via a grouped pipeline
4. **Compiler** — transforms the specification into a versioned OpenAPI document array
5. **Builder** — the unified entry point that orchestrates the pipeline

## Assembler

The Assembler reads spec attributes off your classes, methods, properties and parameters,
and works out which ones belong inside which. An `#[OA\Response]` on a method ends up
inside that method's operation; an `#[OA\Property]` on a class property ends up inside the
class's schema.

Each attribute declares where it can go, rather than the Assembler hard-coding the rules.
That is what lets you introduce your own attributes and have them nest correctly. The
declaration mechanism is described in
[Spec pipeline internals](/dev/pipeline#slot-maps-the-slot-belongs-to-the-parent).

Whatever is left once nesting is resolved is added to the Specification.

## Specification

The Specification is a flat, typed container with one bucket per root attribute type. It holds all attributes collected by the Assembler, organized by type (schemas, operations, pathItems, tags, etc.).

Augmenters read from and write to the Specification's buckets. The container is deliberately simple — no tree structure, no parent pointers. Cross-bucket relationships are resolved by augmenters using reflectors.

## Augmenters

Augmenters form a grouped pipeline that enriches the Specification in three ordered phases:

### Pipeline phases

| Phase       | Purpose                                                       |
| ----------- | ------------------------------------------------------------- |
| **Resolve** | Infer data from PHP reflection and cross-bucket relationships |
| **Reduce**  | Filter or remove entries                                      |
| **Augment** | Add derived metadata                                          |

Each augmenter implements `PipeInterface` and receives the full Specification, and those
within a phase run in registration order. Which augmenters belong to each phase, in the
order they run, is listed in the [Augmenters reference](/reference/augmenters) — generated
from the pipeline itself, so it cannot fall out of step with the code.

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
