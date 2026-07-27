# Using the `Builder`

## Introduction

The `Builder` class is the recommended entry point for generating OpenAPI documents from PHP code. It provides a clean, fluent API and returns a `Result` object with access to the generated spec, scanned files, and validation diagnostics.

## Basic usage

```php
$result = (new \OpenApi\Builder())
    ->addSource('src/Controllers')
    ->addSource('src/Models')
    ->build();

echo $result->toYaml();
```

## Processing modes

The Builder supports three processing modes via `setMode()`:

### Classic (default)

Scans source files for annotations/attributes and assembles the OpenAPI document via the Generator pipeline. This is the stable, production-ready mode.

```php
$builder->setMode('classic');
```

### Spec (beta) {#mode-spec}

Runs the new spec attributes pipeline end-to-end: Assembler → Augmenters → Compiler. Uses pure PHP 8.1+ attributes from the `OpenApi\Spec` namespace with typed DTOs and version-aware compilers.

```php
$builder->setMode('spec');
```

### Hybrid (beta) {#mode-hybrid}

Uses the classic Generator for scanning, then bridges the result into the spec pipeline's augmenters and compilers. A transition path for existing projects that want access to the new augmenter pipeline without rewriting all annotations.

```php
$builder->setMode('hybrid');
```

::: tip Choosing a mode
See the [Processing Modes](/guide/modes) guide for a full comparison and migration path.
:::

## API

### Sources

```php
// Add sources one at a time
$builder->addSource('src/Controllers');
$builder->addSource(new \OpenApi\Utils\SourceFinder('src/', ['tests']));

// Or set all at once
$builder->setSources(['src/Controllers', 'src/Models']);
```

Sources can be directory paths, file paths, `\SplFileInfo`, `\Symfony\Component\Finder\Finder` instances, or nested iterables of these.

### Version

```php
$builder->setVersion('3.1.0');
```

Sets the target OpenAPI version. Version resolution order:
1. Explicit `setVersion()` call (highest priority)
2. Version declared in the source `#[OA\OpenApi(version: '...')]` attribute
3. Falls back to `3.0.0`

### Logger

```php
$builder->setLogger($psrLogger);
```

Accepts any PSR-3 logger. Defaults to `NullLogger` (silent). The CLI command sets its own console logger.

### Generator configuration (classic mode)

For advanced Generator configuration (custom analysers, processors, aliases, type resolvers), use `withGenerator()`:

```php
$builder->withGenerator(function (\OpenApi\Generator $generator) {
    $generator->setAnalyser($customAnalyser);
    $generator->setConfig(['operationId.hash' => false]);
    $generator->withProcessorPipeline(function ($pipeline) {
        $pipeline->remove(\OpenApi\Processors\CleanUnusedComponents::class);
    });
});
```

The callable receives a pre-configured `Generator` instance and may either modify it in-place or return a new instance.

### Augmenter configuration (spec/hybrid mode) {#augmenters}

For spec and hybrid modes, use `withAugmenters()` to configure the augmenter pipeline:

```php
use OpenApi\Augmenter;

$builder->withAugmenters(function (\OpenApi\Utils\Pipeline $pipeline) {
    // Disable an augmenter
    $pipeline->get(Augmenter\Cleanup::class)?->setEnabled(false);

    // Configure operationId generation
    $pipeline->get(Augmenter\OperationIds::class)?->setHash(true);

    // Filter to specific paths/tags
    $pipeline->get(Augmenter\PathFilter::class)
        ?->setPathFilter('/^\/api\/v2/')
        ?->setTagFilter('/^(Users|Products)$/');

    // Insert a custom augmenter
    $pipeline->insert(new CustomAugmenter(), Augmenter\Inheritance::class);

    // Remove an augmenter entirely
    $pipeline->remove(Augmenter\EnumDescriptions::class);
});
```

The pipeline is grouped into three phases that run in order: **resolve** → **reduce** → **augment**. See the [Augmenters reference](/reference/augmenters) for the full list and their configuration options.

### Attribute factory configuration (spec mode) {#attribute-factory}

Use `withAttributeFactory()` to add custom attribute translators:

```php
use OpenApi\Utils\AttributeFactory;

$builder->withAttributeFactory(function (AttributeFactory $factory): void {
    $factory->getTranslators()->add(new SymfonyValidationTranslator());
});
```

Translators convert non-OA attributes (e.g. Symfony `#[Assert\*]`, framework route annotations) into spec DTOs during assembly.

## Result

The `build()` method returns a `\OpenApi\Builder\Result` instance:

```php
$result = $builder->build();

$result->isValid();     // bool — true if a spec was generated
$result->toArray();     // array — the spec as a PHP array
$result->toJson();      // string — JSON output
$result->toYaml();      // string — YAML output
$result->files();       // string[] — scanned source files
$result->log();         // array — all log entries [{level, message}, ...]
$result->warnings();    // string[] — warning messages
$result->errors();      // string[] — error messages
```

## Full example (spec mode)

```php
use OpenApi\Builder;
use OpenApi\Builder\Mode;
use OpenApi\Augmenter;

$result = (new Builder())
    ->setMode(Mode::SPEC)
    ->setVersion('3.1.0')
    ->addSource('src/Api')
    ->withAugmenters(function (\OpenApi\Utils\Pipeline $pipeline) {
        $pipeline->get(Augmenter\Cleanup::class)?->setEnabled(false);
        $pipeline->get(Augmenter\OperationIds::class)?->setHash(true);
    })
    ->build();

echo $result->toYaml();
```
