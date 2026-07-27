# 🧪 Processing Modes

Swagger-php supports three processing modes that control how your source code is transformed into an OpenAPI document. Each mode uses a different internal pipeline, but all produce the same OpenAPI output format.

## Overview

| | Classic | Hybrid | Spec |
|---|---|---|---|
| **Status** | Stable | Beta | Beta |
| **Attributes** | `OpenApi\Attributes` | `OpenApi\Attributes` | `OpenApi\Spec` |
| **Annotations** | Yes | Yes | No |
| **Pipeline** | Generator → Processors | Generator → HybridBridge → Augmenters → Compiler | Assembler → Augmenters → Compiler |
| **PHP requirement** | 8.1+ | 8.1+ | 8.1+ |
| **Best for** | Existing projects | Gradual migration | New projects |

## Classic (default)

The classic mode scans source files for `OpenApi\Attributes` (and legacy `OpenApi\Annotations`) and assembles the OpenAPI document via the Generator pipeline with its processor chain.

This is the stable, production-ready mode and the default for all existing projects.

```php
use OpenApi\Builder;

$result = (new Builder())
    ->addSource('src/')
    ->build();

$result->toYaml();
```

Classic mode gives you access to the full `Generator` API including custom processors, analysers, and configuration options via `withGenerator()`.

## Spec (beta) {#spec}

Spec mode is a ground-up reimplementation of the pipeline using pure PHP 8.1+ attributes from the `OpenApi\Spec` namespace. It introduces:

- **Typed DTOs** — attributes are simple data containers with constructor-promoted properties
- **Slot-map nesting** — explicit `merge()`/`contains()` maps replace reflection-based nesting resolution
- **Grouped augmenters** — a three-phase pipeline (resolve → reduce → augment) that's easy to extend and configure
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

Spec mode uses the `OpenApi\Spec` namespace (`use OpenApi\Spec as OA;`) with a cleaner attribute API. See [Using Spec Attributes](/guide/spec-attributes) for a full guide.

::: warning Beta
Spec mode is feature-complete but still beta. The attribute API may evolve based on feedback before being promoted to default in a future major version.
:::

## Hybrid (beta) {#hybrid}

Hybrid mode uses the classic Generator for scanning (so your existing `OpenApi\Attributes` annotations work unchanged), then bridges the result into the spec pipeline's augmenters and compilers.

This gives you access to the new augmenter pipeline — with its cleaner extension model and version-aware compilation — without rewriting any attribute code.

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

// String value
$builder->setMode('spec');

// Or enum
$builder->setMode(Mode::SPEC);
```

## Behavioral differences

The three modes produce equivalent OpenAPI output for the same logical API. However, there are some differences in how they process source code:

| Behavior | Classic | Hybrid                                               | Spec |
|---|---|------------------------------------------------------|---|
| Annotation support (`/** @OA\... */`) | Yes | Yes                                                  | No |
| `MergeJsonContent` / `MergeXmlContent` | Yes | Yes                                                  | No (use `MediaType` directly) |
| Processor chain (`withGenerator()`) | Yes | Scanning only (`MergeJsonContent`/`MergeXmlContent`) | No |
| Augmenter pipeline (`withAugmenters()`) | No | Yes                                                  | Yes |
| Version-aware compilation | No (single serializer) | Yes                                                  | Yes |

## Migration path

The recommended migration path is:

1. **Classic → Hybrid** — change `setMode('hybrid')` and verify output is unchanged. No code changes needed. This gives you access to the augmenter pipeline.

2. **Hybrid → Spec** — when starting new code, use `OpenApi\Spec` attributes. Existing `OpenApi\Attributes` code continues to work via hybrid mode.

3. **Full Spec** — once all code uses `OpenApi\Spec` attributes, switch to `setMode('spec')` for the cleanest pipeline.

::: tip Version timeline
- **v6** — spec/hybrid ship as opt-in beta. Classic remains default.
- **v7** — hybrid becomes the default mode. Classic still available. `setMode()` and all classic code deprecated
- **v8** — classic removed. `setMode()` removed. Spec becomes default. spec code might move to `OpenApi\Attributes`.
:::
