# 🧪 Processing Modes

Swagger-php supports three processing modes that control how your source code is transformed into an OpenAPI document. Each uses a different internal pipeline.

## Overview

|                 | Classic                | Hybrid                                                       | Spec                                         |
| --------------- | ---------------------- | ------------------------------------------------------------ | -------------------------------------------- |
| **Status**      | Stable                 | Beta                                                         | Beta                                         |
| **Attributes**  | `OpenApi\Attributes`   | `OpenApi\Attributes`                                         | `OpenApi\Spec`                               |
| **Annotations** | Yes                    | Yes                                                          | No                                           |
| **Pipeline**    | Generator → Processors | Generator → HybridBridge → Resolver → Augmenters → Compiler  | Assembler → Resolver → Augmenters → Compiler |
| **Best for**    | Existing projects      | Gradual migration                                            | New projects                                 |

## Classic (default)

The classic mode scans source files for `OpenApi\Attributes` (and legacy `OpenApi\Annotations`) and assembles the OpenAPI document via the Generator pipeline with its processor chain.

```php
use OpenApi\Builder;

$result = (new Builder())
    ->addSource('src/')
    ->build();

$result->toYaml();
```

Classic mode gives you access to the full `Generator` API including custom processors, analysers, and configuration options via `withGenerator()`.

## Spec (beta) {#spec}

Spec mode is a ground-up reimplementation of the pipeline using attributes from the `OpenApi\Spec` namespace. It introduces:

- **Typed DTOs** — attributes are simple data containers with constructor-promoted properties
- **Slot-map nesting** — explicit `merge()`/`contained()` maps replace reflection-based nesting
- **Grouped augmenters** — a three-phase pipeline (resolve → reduce → augment) with explicit ordering
- **Version-aware compilers** — separate compilers for OpenAPI 3.0, 3.1, and 3.2

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

Hybrid mode uses the classic Generator for scanning (so your existing `OpenApi\Attributes` annotations work unchanged), then bridges the result into the spec pipeline's augmenters and compilers.

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

Hybrid mode is the recommended transition path for existing projects that want to benefit from the new pipeline incrementally.

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

| Behavior                                | Classic                | Hybrid                                               | Spec                          |
| --------------------------------------- | ---------------------- | ---------------------------------------------------- | ----------------------------- |
| Annotation support (`/** @OA\... */`)   | Yes                    | Yes                                                  | No                            |
| `MergeJsonContent` / `MergeXmlContent`  | Yes                    | Yes                                                  | Yes (via `OA\MediaType\Json`) |
| Processor chain (`withGenerator()`)     | Yes                    | Scanning only (`MergeJsonContent`/`MergeXmlContent`) | No                            |
| Resolver (`withResolver()`)             | No                     | Yes                                                  | Yes                           |
| Augmenter pipeline (`withAugmenters()`) | No                     | Yes                                                  | Yes                           |
| Version-aware compilation               | No (single serializer) | Yes                                                  | Yes                           |

## Migration path

The recommended migration path is:

1. **Classic → Hybrid** — change `setMode(Mode::HYBRID)` and verify output is unchanged. No code changes needed. This gives you access to the augmenter pipeline.

2. **Hybrid → Spec** — when starting new code, use `OpenApi\Spec` attributes. Existing `OpenApi\Attributes` code continues to work via hybrid mode.

3. **Full Spec** — once all code uses `OpenApi\Spec` attributes, switch to `setMode(Mode::SPEC)`.

::: tip Version timeline
- **v6** — spec/hybrid ship as opt-in beta. Classic remains default.
- **v7** — hybrid becomes the default mode. Classic still available. `setMode()` and all classic code deprecated.
- **v8** — classic removed. `setMode()` removed. Spec becomes default. Spec code might move to `OpenApi\Attributes`.
:::
