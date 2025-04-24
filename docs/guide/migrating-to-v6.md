# Migrating to v6

## Overview

v6 is mostly a cleanup release with updated dependencies. The main changes are:

* Minimum required PHP version is now 8.1
* ...

For most installations upgrading should not require any changes.

## Removed deprecated elements
### `\Openapi\Generator::getProcessors()` and `\Openapi\Generator::setProcessors()`
Use `getProcessorPipeline()` and `setProcessorPipeline(new Pipeline(...))` methods instead
