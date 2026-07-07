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

Sets the target OpenAPI version. If not set, defaults to the version declared in your `#[OA\OpenApi]` attribute.

### Logger

```php
$builder->setLogger($psrLogger);
```

Accepts any PSR-3 logger. Defaults to `DefaultLogger` (which writes warnings to PHP's error log).

### Generator configuration

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
