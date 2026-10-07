# 🧪 Processing Modes

Swagger-php supports three processing modes, which control how source code is turned into an OpenAPI document. Each uses a different internal pipeline.

## Overview

|                 | Classic                | Hybrid                                                       | Spec                                         |
| --------------- | ---------------------- | ------------------------------------------------------------ | -------------------------------------------- |
| **Status**      | Stable                 | Beta                                                         | Beta                                         |
| **Attributes**  | `OpenApi\Attributes`   | `OpenApi\Attributes`                                         | `OpenApi\Spec`                               |
| **Annotations** | Yes                    | Yes                                                          | No                                           |
| **Pipeline**    | Generator → Processors | Assembler → Resolver → HybridBridge → Augmenters → Compiler  | Assembler → Resolver → Augmenters → Compiler |

## Classic (default)

Classic mode scans source files for `OpenApi\Attributes` (and legacy `OpenApi\Annotations`) and builds the OpenAPI document through the `Generator` and its processor chain.

```php
use OpenApi\Builder;

$result = (new Builder())
    ->addSource('src/')
    ->build();

$result->toYaml();
```

Classic mode gives access to the full `Generator` API through `withGenerator()`, including custom processors, analysers and configuration options.

## Spec (beta) {#spec}

Spec mode reimplements the pipeline from the ground up, using attributes from the `OpenApi\Spec` namespace. It introduces:

- **Typed DTOs**: attributes are simple data containers with constructor-promoted properties
- **Slot-map nesting**: explicit `merge()`/`contained()` maps replace reflection-based nesting
- **Grouped augmenters**: a three-phase pipeline (resolve → reduce → augment) with explicit ordering
- **Version-aware compilers**: separate compilers for OpenAPI 3.0, 3.1 and 3.2

```php
use OpenApi\Builder;
use OpenApi\Builder\Mode;

$result = (new Builder())
    ->setMode(Mode::SPEC)
    ->addSource('src/')
    ->build();

$result->toYaml();
```

Spec mode uses the `OpenApi\Spec` namespace (`use OpenApi\Spec as OA;`). See [Using Spec Attributes](/guide/spec-attributes) for a full guide.

::: warning Beta
Spec mode is mostly feature-complete but still beta. The attribute API may evolve based on feedback before being promoted to default in a future major version.
:::

## Hybrid (beta) {#hybrid}

Hybrid mode scans with the classic `Generator`, so existing `OpenApi\Attributes` work unchanged. It then bridges the result into the spec pipeline's augmenters and compilers.

This gives you the augmenter pipeline and version-aware compilation without rewriting any attribute code.

```php
use OpenApi\Builder;
use OpenApi\Builder\Mode;

$result = (new Builder())
    ->setMode(Mode::HYBRID)
    ->addSource('src/')
    ->build();

$result->toYaml();
```

Hybrid mode is the recommended transition path for existing projects that move to the new pipeline step by step.

::: warning Disclaimer
Hybrid mode will not work in heavily customized projects like `NelmioApiDocBundle`, or in projects adding custom processors.
:::

## Switching modes

### CLI

```shell
./vendor/bin/openapi src/ --mode spec -o openapi.yaml
./vendor/bin/openapi src/ --mode hybrid -o openapi.yaml
```

### PHP

```php
use OpenApi\Builder;
use OpenApi\Builder\Mode;

$builder->setMode(Mode::SPEC);
// or: $builder->setMode('spec');
```

## Behavioral differences

The modes aim for equivalent output from the same source, but differ in what they accept and in how they can be configured:

| Behavior                                | Classic                | Hybrid                                               | Spec                                   |
| --------------------------------------- | ---------------------- | ---------------------------------------------------- | -------------------------------------- |
| Annotation support (`/** @OA\... */`)   | Yes                    | Yes                                                  | No                                     |
| `MergeJsonContent` / `MergeXmlContent`  | Yes                    | Yes                                                  | Yes (via `OA\MediaType\Json`)          |
| Processor chain (`withGenerator()`)     | Yes                    | Scanning only (`MergeJsonContent`/`MergeXmlContent`) | No                                     |
| Resolver (`withResolver()`)             | No                     | Yes (`Resolver\Reflection` by default)               | Yes (`Resolver\Reflection` by default) |
| Augmenter pipeline (`withAugmenters()`) | No                     | Yes                                                  | Yes                                    |
| Contributions (`withSpecification()`)   | No                     | Yes                                                  | Yes                                    |
| Version-aware compilation               | No (single serializer) | Yes                                                  | Yes                                    |
| Unreferenced components                 | Kept                   | Removed, with a notice                               | Removed, with a notice                 |
| `-c` / `-D` keys                        | Processors, `generator.*` | Augmenters, `generator.*`                         | Augmenters                             |

Classic removes unreferenced components only when asked, with `-c cleanUnusedComponents.enabled=true`.
Hybrid and spec remove them by default and log how many at notice level. `-c cleanup.enabled=false`
keeps them, or in PHP:

```php
use OpenApi\Augmenter;
use OpenApi\Utils\Pipeline;

$builder->withAugmenters(fn (Pipeline $pipeline) => $pipeline->get(Augmenter\Cleanup::class)->setEnabled(false));
```

See [Augmenters](/reference/augmenters) for the `Cleanup` options.

## Migration path

The recommended migration path is:

1. **Classic → Hybrid**: change to `setMode(Mode::HYBRID)`. No code changes are needed, and the augmenter pipeline becomes available. Output stays the same, with two exceptions:

   - Components that no path references are removed, including a schema kept only for client code generation. [Behavioral differences](#behavioral-differences) has the switch that keeps them.

   - `-c` takes augmenter keys rather than processor keys, apart from `generator.*`. `--mode hybrid -D src` lists them.

2. **Hybrid → Spec**: when starting new code, use `OpenApi\Spec` attributes. Existing `OpenApi\Attributes` code keeps working through hybrid mode. The spec attributes are not a one-for-one rename of the classic ones. A reusable attribute takes a single `component:` key, where classic spells the key after its own type (`schema:`, `parameter:`, `request:`, `securityScheme:`). See [Components](/guide/spec-attributes#components).

3. **Full Spec**: once all code uses `OpenApi\Spec` attributes, switch to `setMode(Mode::SPEC)`.

::: tip Version timeline
- **v6**: spec and hybrid ship as opt-in beta. Classic remains the default.
- **v7**: hybrid becomes the default mode. Classic is still available. `setMode()` and all classic code are deprecated.
- **v8**: classic is removed, and so is `setMode()`. Spec becomes the default. Spec attributes move to `OpenApi\Attributes`. For code already written against `OpenApi\Spec`, that move is one `use` line per file. Coming from classic, the component keys are renamed as well.
:::
