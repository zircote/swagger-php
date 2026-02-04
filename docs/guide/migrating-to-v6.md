# Migrating to v6

## Overview

`v6` is mostly a cleanup release, with updated dependencies. The main changes are:

* The minimum required PHP version is now 8.2
* `radebatz/type-info-extras` is now a required dependency
* `TypeInfoTypeResolver` now properly handles composite types (unions and intersections)
* Some deprecations have been removed (see below)
* The `MediaType::encoding` property now only accepts `Encoding` objects (BC break)
* The CLI option for adding a processor has been renamed from `--processor` to `--add-processor (-a)`
* A new CLI option for removing a processor has been added `--remove-processor (-r)`

For most installations upgrading should not require any changes.

## Type resolvers
With `radebatz/type-info-extras` now being a required dependency, the `TypeInfoTypeResolver` is now the de-facto default
resolver.

The `LegacyTypeResolver` can still be used as a drop-in replacement, but is now marked `deprecated` and will be removed
in v7.

## Removed deprecated elements
### Methods `\Openapi\Generator::getProcessors()` and `\Openapi\Generator::setProcessors()`
Use `getProcessorPipeline()` and `setProcessorPipeline(new Pipeline(...))` methods instead

### Static method `\Openapi\Generator::scan()`
Main entry point into the `Generator` is now the **non-static** `generate()` method:
```php
(new Generator())->generate(/* ... */);
```

### `Utils` helper
Most methods in the class were internal to start with and the `Util::finder()` factory methods is now replaced with
the new `SourceFinder` class.
