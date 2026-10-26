# Reference

Depending on how custom your requirements are, using `swagger-php` might be limited to just annotating your code and running the command line tool.

However, `swagger-php` offers more.

* [Spec attributes](spec-attributes.md)

  The attributes in the `OpenApi\Spec` namespace, read in spec and hybrid mode.

* The [`Builder`](builder.md)

  The `\OpenApi\Builder` class is the entry point for generating OpenAPI documents from PHP code.

* [Augmenters](augmenters.md)

  Augmenters enrich the spec-attributes pipeline with inferred data (types, refs, docblocks)
  before compilation. They run in three groups: resolve, reduce, and augment.

* [Extension points](extension-points.md)

  The translators and resolvers the spec pipeline runs by default.

* [Mergers](mergers.md)

  How the spec pipeline decides between two attributes that claim the same key.

* [Inheritance](inheritance.md)

  How PHP class hierarchy maps to OpenAPI `allOf` composition (schemas) and path prefix
  composition (PathItems) in the spec pipeline.

## Classic <Badge type="warning" text="deprecated" />

* [Attributes](attributes.md)

  The attributes in the `OpenApi\Attributes` namespace.

* [Docblock annotations](annotations.md)

  The `@OA` annotations in docblocks.

* The [`Generator`](generator.md)

  The `\OpenApi\Generator` class, used directly or via the Builder's `withGenerator()` method.

* [Processors](processors.md)

  The processors that convert the collected annotations into a complete OpenAPI document.
  Custom processors can be added, and existing ones removed.
