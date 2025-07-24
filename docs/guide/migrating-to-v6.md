# Migrating to v6

## Overview

v6 is mostly a cleanup release with updated dependencies. The main changes are:

* Minimum required PHP version is now 8.1
* ...

For most installations upgrading should not require any changes.

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
