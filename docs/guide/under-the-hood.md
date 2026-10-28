# Under the hood

## Processing flow

The `Builder` runs one of three pipelines, depending on the [mode](/guide/modes).

In spec mode, the Assembler reads the spec attributes into a `Specification`. The Resolver adds
the classes it references, the augmenters enrich it, and a compiler for the target OpenAPI
version writes the document. [Spec pipeline architecture](/reference/architecture) describes
each step.

Hybrid mode scans with the classic `Generator` and its analyser, then hands the result to the
same augmenters and compilers ([Hybrid](/guide/modes#hybrid)).

## The classic pipeline <Badge type="warning" text="deprecated" />

- The `Generator` iterates over the given sources (Symfony `Finder`, file/directory list, etc.)
- The configured analyser (`AnalyserInterface`) reads the files and builds an `Analysis` object.
  The default is the `ReflectionAnalyser`.
- The `Analysis` object and its annotation instances are then processed by the configured processors.
- If enabled, the annotation instances are validated.
- The root `OpenApi` instance then contains all annotation instances and is serialized into YAML/JSON.

### `Context`

Each annotation instance is associated with a unique `Context` instance. This contains details, collected by the parser/analyser,
about the PHP context where the attribute or annotation was found.

Typically, there will be a processor that uses the data to augment the annotation instance.

**Examples of the data collected:**
  - class/interface/trait/enum names
  - property names
  - doctype or native type hints
  - file name and line number

### Analysis

Contains all detected annotation instances and other relevant metadata.

It uses a `SplObjectStorage` instance to store the annotation instances.
