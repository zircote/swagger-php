# Reference

Depending on how custom your requirements are, using `swagger-php` might be limited to just annotating your code and running the command line tool.

However, `swagger-php` offers more.

* [Attributes](attributes.md)

  The new way of adding meta-data to your codebase. Requires PHP 8.2

* [Docblock annotations](annotations.md)

  The 'traditional' way of documenting your API.

* The [`Builder`](builder.md)

  The `\OpenApi\Builder` class is the recommended entry point for generating OpenAPI documents from PHP code.

* The [`Generator`](generator.md)

  The `\OpenApi\Generator` class can be used directly for advanced use cases or via the Builder's `withGenerator()` method.

* [Processors](processors.md)

  `swagger-php` comes with a list of pre-defined processors that convert the raw data to a
  complete OpenAPI document.
  Custom processors can be added, and existing ones removed, to tweak `swagger-php` to your requirements.

* [Augmenters](augmenters.md)

  Augmenters enrich the spec-attributes pipeline with inferred data (types, refs, docblocks)
  before compilation. They run in three groups: resolve, reduce, and augment.

* [Inheritance](inheritance.md)

  How PHP class hierarchy maps to OpenAPI `allOf` composition (schemas) and path prefix
  composition (PathItems) in the spec pipeline.
