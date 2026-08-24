# Using the `Builder`

## Introduction

The `Builder` class is the recommended entry point for generating OpenAPI documents from PHP code. Its setters are chainable, and `build()` returns a `Result` giving access to the generated document, the files that were scanned, and the warnings and errors collected along the way.

## Basic usage

```php
$result = (new \OpenApi\Builder())
    ->addSource('src/Controllers')
    ->addSource('src/Models')
    ->build();

echo $result->toYaml();
```

## Processing modes

The Builder supports three processing modes via `setMode(string|Mode $mode)`:

### Classic (default)

Scans source files for annotations/attributes and assembles the OpenAPI document via the Generator pipeline.

```php
use OpenApi\Builder\Mode;

$builder->setMode(Mode::CLASSIC);
// or: $builder->setMode('classic');
```

### Spec (beta) {#mode-spec}

Runs the spec attributes pipeline end-to-end: Assembler → Resolver → Augmenters → Compiler. Uses attributes from the `OpenApi\Spec` namespace with typed DTOs and version-aware compilers.

```php
$builder->setMode(Mode::SPEC);
// or: $builder->setMode('spec');
```

### Hybrid (beta) {#mode-hybrid}

Uses the classic Generator for scanning, then bridges the result into the spec pipeline's augmenters and compilers. A transition path for existing projects that want access to the new augmenter pipeline without rewriting all annotations.

```php
$builder->setMode(Mode::HYBRID);
// or: $builder->setMode('hybrid');
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

#### Reflector sources (spec/hybrid mode)

In spec and hybrid mode, you can pass `\Reflector` instances (e.g. `\ReflectionClass`) directly instead of file paths. This is useful when you already have reflection objects available or want to build a spec from a specific set of classes without file scanning:

```php
use OpenApi\Builder;
use OpenApi\Builder\Mode;

$result = (new Builder())
    ->setMode(Mode::SPEC)
    ->addSource([
        new \ReflectionClass(App\Controllers\PetController::class),
        new \ReflectionClass(App\Models\Pet::class),
    ])
    ->build();
```

::: warning
Reflector sources are not supported in classic mode — they require the spec or hybrid pipeline.
:::

### Version

```php
$builder->setVersion('3.1.0');
```

Sets the target OpenAPI version. Version resolution order:
1. Explicit `setVersion()` call (highest priority)
2. Version declared in the source `#[OA\OpenApi(version: '...')]` attribute
3. Falls back to `3.0.0` (classic) or `3.1.0` (spec/hybrid)

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

    // Filter to specific paths/tags (regular expressions, with delimiters)
    $pipeline->get(Augmenter\PathFilter::class)
        ?->setPaths(['/^\/api\/v2/'])
        ->setTags(['/^(Users|Products)$/']);

    // Insert a custom augmenter
    $pipeline->insert(new CustomAugmenter(), Augmenter\Inheritance::class);

    // Remove an augmenter entirely
    $pipeline->remove(Augmenter\EnumDescriptions::class);
});
```

The pipeline is grouped into three phases that run in order: **resolve** → **reduce** → **augment**. See the [Augmenters reference](/reference/augmenters) for the full list and configuration options, and the [Augmenters section](/reference/architecture#augmenters) in the architecture docs for pipeline design and writing custom augmenters.

### Resolver configuration (spec/hybrid mode) {#resolver}

The resolver handles FQCNs that are referenced by the specification but have no matching component. It runs after assembly but before augmenters, so newly added schemas are processed by the full augmenter pipeline in a single pass.

`Resolver\Reflection` is registered by default: it collects the referenced class with the assembler in use, so adding a single controller is enough to pick up everything it references — no need to list all related classes as sources.

Use `withResolver()` to add your own:

```php
use OpenApi\Resolver;
use OpenApi\Utils\TypedList;

$builder->withResolver(function (Resolver $resolver) {
    $resolver->withResolvers(fn (TypedList $resolvers) => $resolvers
        ->add(new MyResolver()));
});
```

Resolvers implement `OpenApi\Contracts\ResolverInterface` and receive the FQCN and the `Assembler` in use. The first one to return `true` claims the FQCN. See the [Resolver section](/reference/architecture#resolver) in the architecture docs for details, including how to reorder or clear the chain.

### Attribute factory configuration (spec mode) {#attribute-factory}

Use `withAttributeFactory()` to add custom attribute translators:

```php
use OpenApi\Utils\AttributeFactory;

$builder->withAttributeFactory(function (AttributeFactory $factory): void {
    $factory->getTranslators()->add(new SymfonyValidationTranslator());
});
```

Translators convert non-OA attributes (e.g. Symfony `#[Assert\*]`, framework route annotations) into spec DTOs during assembly. See the [Assembler section](/reference/architecture#assembler) in the architecture docs for how assembly and slot-map nesting work.

## Result

The `build()` method returns a `\OpenApi\Builder\Result` instance:

```php
$result = $builder->build();

$result->isValid();       // bool — true if a document was produced and no errors were reported
$result->toArray();       // array — the spec as a PHP array
$result->toJson();        // string — JSON output
$result->toYaml();        // string — YAML output
$result->saveAs($file);   // void — write to disk, format from the extension
$result->files();         // string[] — scanned source files
$result->log();           // array — all log entries [{level, message}, ...]
$result->warnings();      // string[] — warning messages
$result->errors();        // string[] — error messages
$result->specification(); // ?Specification — spec/hybrid only, null in classic
$result->openApi();       // ?OA\OpenApi — classic only, null in spec/hybrid
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
